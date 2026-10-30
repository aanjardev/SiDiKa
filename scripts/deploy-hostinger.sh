#!/usr/bin/env bash
# SiDiKa deployment for Hostinger shared hosting. Run as the hosting account, never sudo.
set -Eeuo pipefail
umask 022
log() { printf '\n[SiDiKa] %s\n' "$*"; }
die() { printf '\nERROR: %s\n' "$*" >&2; exit 1; }
trap 'printf "\nDeployment berhenti pada baris %s. Perbaiki error di atas, lalu jalankan ulang.\n" "$LINENO" >&2' ERR

if [[ "${1:-}" == '--help' || "${1:-}" == '-h' ]]; then
  cat <<'HELP'
Usage: bash deploy-hostinger.sh [FOLDER_REPO] [--check | --finish-storage]
Tanpa FOLDER_REPO: gunakan folder sekarang atau cari repo SiDiKa di ~/domains.
--check: hanya periksa repo, lingkungan, dan posisi main; tidak install/build/migrasi.
--finish-storage: selesaikan pemeriksaan/pemetaan storage tanpa install, build, atau migrasi.
Pilihan: SIDIKA_PHP_BIN=/path/php untuk memilih PHP CLI yang sesuai hPanel.
Skrip tidak pull/reset kode, mengubah APP_KEY, menjalankan seeder, atau menghapus upload.
Jika /public/storage terbukti valid, hanya PUBLIC_STORAGE_URL pada .env yang diatur dengan backup.
HELP
  exit 0
fi
check_only=0
finish_storage=0
root_arg=''
for arg in "$@"; do
  case "$arg" in
    --check) check_only=1 ;;
    --finish-storage) finish_storage=1 ;;
    --*) die "Opsi tidak dikenal: $arg" ;;
    *) [[ -z "$root_arg" ]] || die 'Berikan satu folder repo saja.'; root_arg="$arg" ;;
  esac
done
[[ "$check_only" != 1 || "$finish_storage" != 1 ]] || die 'Pilih --check atau --finish-storage, bukan keduanya.'
for tool in git curl tar gzip awk sha256sum readlink ln grep cp mv chmod wc; do
  command -v "$tool" >/dev/null || die "Program $tool tidak tersedia di hosting."
done
[[ "$(uname -s)" == Linux ]] || die 'Skrip ini untuk SSH Linux Hostinger, bukan PowerShell/Windows.'

is_sidika() {
  [[ -f "$1/artisan" && -f "$1/composer.lock" && -f "$1/package-lock.json" ]] || return 1
  local origin
  origin=$(git -C "$1" remote get-url origin 2>/dev/null) || return 1
  [[ "$origin" =~ github\.com[:/]aanjardev/SiDiKa(\.git)?/?$ ]]
}
if [[ -n "$root_arg" ]]; then
  [[ -d "$root_arg" ]] || die "Folder tidak ditemukan: $root_arg"
  repo=$(cd -- "$root_arg" && pwd -P)
  is_sidika "$repo" || die 'Folder bukan root Laravel dari repo aanjardev/SiDiKa.'
elif is_sidika "$PWD"; then
  repo=$(pwd -P)
else
  [[ -d "$HOME/domains" ]] || die 'Masuk ke folder repo lalu jalankan ulang, atau berikan path repo.'
  candidates=()
  artisan_files=$(find "$HOME/domains" -maxdepth 7 -type d \( -name vendor -o -name node_modules -o -name .git \) -prune -o -type f -name artisan -print 2>/dev/null || true)
  while IFS= read -r artisan_file; do
    [[ -n "$artisan_file" ]] || continue
    candidate=$(cd -- "$(dirname "$artisan_file")" && pwd -P)
    if is_sidika "$candidate"; then candidates+=("$candidate"); fi
  done <<SIDIKA_REPO_LIST
$artisan_files
SIDIKA_REPO_LIST
  [[ ${#candidates[@]} -gt 0 ]] || die 'Repo tidak ditemukan. Jalankan dengan path folder yang berisi artisan.'
  if [[ ${#candidates[@]} -ne 1 ]]; then
    printf 'Ada beberapa repo:\n'; printf '  %s\n' "${candidates[@]}"
    die 'Jalankan ulang dengan satu path repo yang dimaksud.'
  fi
  repo=${candidates[0]}
fi
cd -- "$repo"
log "Repo: $repo"
[[ -f .env ]] || die '.env belum tersedia. Konfigurasikan lewat hosting; jangan buat APP_KEY baru pada aplikasi existing.'
[[ -d storage && -w storage && -d bootstrap/cache && -w bootstrap/cache ]] || die 'storage dan bootstrap/cache harus writable oleh akun hosting.'
if [[ "$finish_storage" != 1 ]]; then
# Ignore generated files only; never reset or overwrite uncommitted application source.
dirty_source=$(git diff --name-only HEAD -- . ':!public/build' ':!public/css' ':!storage' ':!bootstrap/cache')
[[ -z "$dirty_source" ]] || { printf '%s\n' "$dirty_source"; die 'Ada perubahan source lokal. Simpan/selesaikan perubahan tersebut sebelum deploy.'; }
# Hostinger keeps its root front controller/rewrite outside the tracked Laravel public directory.
untracked_source=$(git ls-files --others --exclude-standard -- . ':!public/build' ':!public/css' ':!storage' ':!bootstrap/cache' ':(top,exclude).htaccess' ':(top,exclude)index.php')
[[ -z "$untracked_source" ]] || { printf '%s\n' "$untracked_source"; die 'Ada source lokal yang belum tracked Git. Simpan/selesaikan file tersebut sebelum deploy.'; }
log 'Memeriksa main terbaru dari GitHub (tanpa pull/reset).'
GIT_TERMINAL_PROMPT=0 git fetch origin main
[[ "$(git rev-parse HEAD)" == "$(git rev-parse FETCH_HEAD)" ]] || die 'HEAD belum sama dengan main terbaru. Jalankan git pull --ff-only origin main lalu ulangi skrip.'
fi
log "Commit: $(git rev-parse --short HEAD)"
php_bin=${SIDIKA_PHP_BIN:-$(command -v php || true)}
[[ -n "$php_bin" && -x "$php_bin" ]] || die 'PHP CLI tidak ditemukan. Tentukan SIDIKA_PHP_BIN.'
"$php_bin" -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);' || die 'Gunakan PHP CLI >= 8.2 yang sesuai versi PHP website di hPanel.'
"$php_bin" -v | head -n 1
if [[ "$check_only" == 1 ]]; then
  command -v node >/dev/null && node --version || true
  command -v npm >/dev/null && npm --version || true
  command -v composer >/dev/null && composer --version || true
  log 'Preflight selesai. Belum ada install/build/migrasi.'
  exit 0
fi
[[ "$finish_storage" != 1 || -f vendor/autoload.php ]] || die 'vendor belum tersedia. Jalankan deployment lengkap lebih dulu.'
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache
command -v flock >/dev/null || die 'flock tidak tersedia; hentikan agar dua deploy tidak berjalan bersamaan.'
exec 9>storage/framework/sidika-deploy.lock
flock -n 9 || die 'Deployment lain sedang berjalan.'

# Official user-local tools only; no sudo, no changes to shell startup files.
tools_root="$HOME/.local/share/sidika-deploy"
mkdir -p "$tools_root"
tmp_dir=$(mktemp -d "$tools_root/tmp.XXXXXX")
probe_path=''
cleanup() {
  if [[ -n "$probe_path" && -f vendor/autoload.php ]]; then
    SIDIKA_PROBE_PATH="$probe_path" "$php_bin" -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); Illuminate\Support\Facades\Storage::disk("public")->delete([getenv("SIDIKA_PROBE_PATH"),getenv("SIDIKA_PROBE_PATH").".txt"]);' >/dev/null 2>&1 || true
  fi
  # Only explicitly named files created by this script are removed.
  rm -f -- "$tmp_dir/SHASUMS256.txt" "$tmp_dir/composer-setup.php" "$tmp_dir/composer.sig" "$tmp_dir/storage-probe.png" "$tmp_dir/storage-probe.txt" "$tmp_dir/storage-probe.headers" "$tmp_dir/storage-probe-image.headers" "$tmp_dir/storage-image-info.json"
  [[ -z "${node_archive:-}" ]] || rm -f -- "$tmp_dir/$node_archive"
  [[ -z "${htaccess_tmp:-}" ]] || rm -f -- "$htaccess_tmp"
  rmdir -- "$tmp_dir" 2>/dev/null || true
}
trap cleanup EXIT
backup_dir="$HOME/.local/share/sidika-deploy/backups"
mkdir -p "$backup_dir"
if [[ "$finish_storage" != 1 ]]; then
node_compatible() {
  command -v node >/dev/null && command -v npm >/dev/null || return 1
  node -e 'const [a,b]=process.versions.node.split(".").map(Number); process.exit((a===20&&b>=19)||(a===22&&b>=12)||a>22 ? 0:1)'
}
if ! node_compatible; then
  log 'Menyiapkan Node 22 lokal untuk Vite 7.'
  case "$(uname -m)" in x86_64) node_arch=x64 ;; aarch64|arm64) node_arch=arm64 ;; *) die 'Arsitektur Node tidak didukung oleh skrip ini.' ;; esac
  curl -fLsS --retry 2 https://nodejs.org/dist/latest-v22.x/SHASUMS256.txt -o "$tmp_dir/SHASUMS256.txt"
  node_record=$(awk -v arch="$node_arch" '$2 ~ "^node-v22\\.[0-9]+\\.[0-9]+-linux-" arch "\\.tar\\.gz$" { print $1, $2; exit }' "$tmp_dir/SHASUMS256.txt")
  read -r node_sha node_archive <<SIDIKA_NODE_RECORD
$node_record
SIDIKA_NODE_RECORD
  [[ "$node_archive" =~ ^node-v22\.[0-9]+\.[0-9]+-linux-(x64|arm64)\.tar\.gz$ && "$node_sha" =~ ^[0-9a-f]{64}$ ]] || die 'Metadata unduhan Node tidak valid.'
  node_home="$tools_root/${node_archive%.tar.gz}"
  if [[ ! -x "$node_home/bin/node" ]]; then
    [[ ! -e "$node_home" ]] || die "Folder instalasi Node tidak lengkap: $node_home"
    curl -fLsS --retry 2 "https://nodejs.org/dist/latest-v22.x/$node_archive" -o "$tmp_dir/$node_archive"
    [[ "$(sha256sum "$tmp_dir/$node_archive" | awk '{print $1}')" == "$node_sha" ]] || die 'Checksum Node tidak cocok.'
    tar -xzf "$tmp_dir/$node_archive" -C "$tools_root"
  fi
  export PATH="$node_home/bin:$PATH"
  node_compatible || die 'Node hasil instalasi tidak dapat berjalan. Hosting harus mendukung binary Node Linux.'
fi
log "Node $(node --version), npm $(npm --version)"
if command -v composer >/dev/null; then
  composer_cmd=(composer)
else
  composer_phar="$tools_root/composer.phar"
  if [[ ! -f "$composer_phar" ]]; then
    log 'Menyiapkan Composer lokal dengan verifikasi checksum installer.'
    curl -fLsS --retry 2 https://composer.github.io/installer.sig -o "$tmp_dir/composer.sig"
    curl -fLsS --retry 2 https://getcomposer.org/installer -o "$tmp_dir/composer-setup.php"
    [[ "$("$php_bin" -r 'echo hash_file("sha384", $argv[1]);' "$tmp_dir/composer-setup.php")" == "$(tr -d '\r\n' < "$tmp_dir/composer.sig")" ]] || die 'Checksum installer Composer tidak cocok.'
    "$php_bin" "$tmp_dir/composer-setup.php" --install-dir="$tools_root" --filename=composer.phar --2
  fi
  composer_cmd=("$php_bin" "$composer_phar")
fi
log 'Memasang dependensi PHP sesuai composer.lock.'
"${composer_cmd[@]}" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
"${composer_cmd[@]}" check-platform-reqs --no-dev

log 'Menyimpan backup aset sebelum build.'
backup_dir="$HOME/.local/share/sidika-deploy/backups"
mkdir -p "$backup_dir"
asset_dirs=()
for asset_dir in public/build public/css; do [[ ! -d "$asset_dir" ]] || asset_dirs+=("$asset_dir"); done
if [[ ${#asset_dirs[@]} -gt 0 ]]; then
  asset_backup="$backup_dir/assets-$(date +%Y%m%d-%H%M%S)-$$.tar.gz"
  tar -czf "$asset_backup" "${asset_dirs[@]}"
  log "Backup aset: $asset_backup"
fi
log 'Memasang dependensi npm dan build aset produksi.'
npm ci --include=dev --no-audit --no-fund
npm run build
npm run copy-css
node <<'NODE'
const fs=require('node:fs'), path=require('node:path');
const root='public/build', manifest=JSON.parse(fs.readFileSync(path.join(root,'manifest.json'),'utf8'));
for(const key of ['resources/css/app.css','resources/js/app.js','resources/css/admin/pages/transaction-tables.css']) if(!manifest[key]) throw new Error('Manifest tidak memiliki '+key);
for(const item of Object.values(manifest)) for(const file of [item.file,...(item.css||[])]) if(file&&!fs.existsSync(path.join(root,file))) throw new Error('Aset build hilang: '+file);
console.log('PASS: manifest dan file aset build tersedia.');
NODE
[[ ! -e public/hot ]] || rm -f -- public/hot

log 'Membersihkan cache lama, lalu menjalankan migrasi yang belum diterapkan.'
"$php_bin" artisan config:clear
"$php_bin" artisan route:clear
"$php_bin" artisan view:clear
"$php_bin" artisan migrate --force
fi

log 'Memeriksa tautan storage lokal tanpa menimpa file/folder existing.'
if [[ -L public/storage ]]; then
  [[ "$(readlink -f public/storage)" == "$(readlink -f storage/app/public)" ]] || die 'public/storage menunjuk ke lokasi lain. Periksa link secara manual; skrip tidak menggantinya.'
elif [[ -e public/storage ]]; then
  die 'public/storage adalah folder/file biasa. Jangan hapus isinya; periksa mapping storage secara manual.'
else
  ln -s -- "$(readlink -f storage/app/public)" public/storage
fi
[[ -L public/storage && "$(readlink -f public/storage)" == "$(readlink -f storage/app/public)" ]] || die 'Link public/storage belum terbentuk dengan target yang benar.'
# This repo still calls env() in models/providers. config:cache would break those values.
if [[ "$finish_storage" != 1 ]]; then
log 'Config cache dibiarkan nonaktif karena repo masih membaca env() di model/provider.'
# Several web/API routes use closures. Keep route cache off until that code is refactored.
log 'Route cache dibiarkan nonaktif; view cache tetap dibangun.'
  "$php_bin" artisan view:cache
  "$php_bin" artisan migrate:status
else
  log 'Mode finish-storage: memakai hasil setup yang sudah ada; install, build, dan migrasi tidak dijalankan.'
fi

app_url=$("$php_bin" -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); if(!Illuminate\Support\Facades\Schema::hasColumn("catalog_settings","dinoyo_way_content")) {fwrite(STDERR,"Kolom dinoyo_way_content belum tersedia.\n");exit(1);} echo rtrim(config("app.url"),"/");')
[[ "$app_url" =~ ^https?://[^[:space:]]+$ ]] || die 'APP_URL tidak valid. Perbaiki .env sesuai domain website.'
log 'Memeriksa landing page dan halaman Way lewat HTTP.'
for endpoint in / /dinoyo-kamera-way; do
  status=$(curl -LsS --max-time 30 --max-redirs 4 -o /dev/null -w '%{http_code}' "$app_url$endpoint")
  [[ "$status" == 200 ]] || die "HTTP $status untuk $endpoint. Periksa storage/logs/laravel.log di server."
done
# Verify an unpredictable text token plus a decodable image. CDN re-encoding must not fail the test.
probe_path="catalog/dinoyo-way/sidika-deploy-check-$(date +%s)-$$.png"
probe_record=$(SIDIKA_PROBE_PATH="$probe_path" "$php_bin" -r '
require "vendor/autoload.php";
$app=require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (!function_exists("imagecreatetruecolor")) { fwrite(STDERR,"GD tidak tersedia.\n"); exit(1); }
$disk=Illuminate\Support\Facades\Storage::disk("public");
$path=getenv("SIDIKA_PROBE_PATH");
$token=bin2hex(random_bytes(16));
$image=imagecreatetruecolor(64,48);
imagefill($image,0,0,imagecolorallocate($image,35,78,102));
imagefilledrectangle($image,8,8,40,32,imagecolorallocate($image,218,90,45));
ob_start(); imagepng($image); $png=ob_get_clean(); imagedestroy($image);
if (!$disk->put($path,$png) || !$disk->put($path.".txt",$token)) { fwrite(STDERR,"Gagal menulis probe lokal.\n"); exit(1); }
echo $disk->url($path),"\n",$disk->url($path.".txt"),"\n",$token;
')
{
  IFS= read -r probe_url
  IFS= read -r probe_text_url
  IFS= read -r probe_token
} <<SIDIKA_PROBE_RECORD
$probe_record
SIDIKA_PROBE_RECORD
probe_ok() {
  probe_reason=''
  probe_status=$(curl --compressed -LsS --max-time 30 --max-redirs 4 -H 'Cache-Control: no-cache' -D "$tmp_dir/storage-probe.headers" -o "$tmp_dir/storage-probe.txt" -w '%{http_code}' "$2") || { probe_reason='koneksi token gagal'; return 1; }
  probe_type=$(awk 'tolower($0) ~ /^content-type:/ { sub(/^[^:]*:[ \t]*/, ""); gsub(/\r/, ""); t=$0 } END { print t }' "$tmp_dir/storage-probe.headers")
  [[ "$probe_status" == 200 ]] || { probe_reason="token HTTP $probe_status ($probe_type)"; return 1; }
  [[ "$(cat "$tmp_dir/storage-probe.txt")" == "$probe_token" ]] || { probe_reason="token tidak cocok; HTTP $probe_status, tipe $probe_type, ukuran $(wc -c < "$tmp_dir/storage-probe.txt") byte"; return 1; }
  probe_status=$(curl --compressed -LsS --max-time 30 --max-redirs 4 -H 'Cache-Control: no-cache' -D "$tmp_dir/storage-probe-image.headers" -o "$tmp_dir/storage-probe.png" -w '%{http_code}' "$1") || { probe_reason='koneksi gambar gagal'; return 1; }
  [[ "$probe_status" == 200 ]] || { probe_reason="gambar HTTP $probe_status"; return 1; }
  if ! "$php_bin" -r '$bytes=file_get_contents($argv[1]); $info=@getimagesizefromstring($bytes); $im=@imagecreatefromstring($bytes); if(!$info || $im===false){exit(1);} imagedestroy($im); echo json_encode(["mime"=>$info["mime"],"width"=>$info[0],"height"=>$info[1]]);' "$tmp_dir/storage-probe.png" > "$tmp_dir/storage-image-info.json"; then
    probe_reason="respons HTTP 200 bukan gambar yang dapat didekode"; return 1
  fi
  probe_reason='token benar dan gambar dapat didekode'
  return 0
}
if ! probe_ok "$probe_url" "$probe_text_url"; then
  [[ "$probe_url" == "$app_url/storage/"* ]] || die 'URL disk public berbeda dari APP_URL/storage. Periksa konfigurasi URL storage.'
  default_probe_reason="$probe_reason"
  alternate_probe_url="$app_url/public/storage/$probe_path"
  probe_ok "$alternate_probe_url" "$alternate_probe_url.txt" || die "Storage belum tervalidasi. /storage: $default_probe_reason; /public/storage: $probe_reason. Tidak ada aturan .htaccess yang diubah."
  log 'File foto valid di /public/storage. Mengatur URL disk public tanpa perubahan rewrite.'
  [[ ! -f bootstrap/cache/config.php ]] || die 'Config masih dicache. Jalankan php artisan config:clear lalu ulangi finish-storage.'
  [[ -f .env && ! -L .env ]] || die '.env harus file biasa; tidak mengganti symlink.'
  env_backup="$backup_dir/env-$(date +%Y%m%d-%H%M%S)-$$.bak"
  (umask 077; cp -- .env "$env_backup"; chmod 600 "$env_backup")
  log "Backup konfigurasi: $env_backup"
  storage_config_backup=''
  restore_storage_config() {
    cp -p -- "$env_backup" .env
    if [[ -n "$storage_config_backup" ]]; then cp -p -- "$storage_config_backup" config/filesystems.php; fi
  }
  if ! grep -Fq 'PUBLIC_STORAGE_URL' config/filesystems.php; then
    [[ -f config/filesystems.php && ! -L config/filesystems.php ]] || die 'Konfigurasi filesystem harus file biasa.'
    storage_config_backup="$backup_dir/filesystems-$(date +%Y%m%d-%H%M%S)-$$.php.bak"
    cp -p -- config/filesystems.php "$storage_config_backup"
    SIDIKA_CONFIG_OLD="'url' => env('APP_URL').'/storage'," \
    SIDIKA_CONFIG_NEW="'url' => env('PUBLIC_STORAGE_URL', env('APP_URL').'/storage')," "$php_bin" -r '
$file="config/filesystems.php";
$source=file_get_contents($file);
$old=getenv("SIDIKA_CONFIG_OLD"); $new=getenv("SIDIKA_CONFIG_NEW");
if (substr_count($source,$old)!==1) { fwrite(STDERR,"Format config berbeda; tidak mengubah source.\n"); exit(1); }
$updated=str_replace($old,$new,$source);
if (file_put_contents($file,$updated,LOCK_EX)!==strlen($updated)) exit(1);
' || { restore_storage_config; die 'Konfigurasi filesystem tidak cocok; backup telah dipulihkan.'; }
    "$php_bin" -l config/filesystems.php >/dev/null || { restore_storage_config; die 'Sintaks konfigurasi gagal; backup telah dipulihkan.'; }
    log "Dukungan PUBLIC_STORAGE_URL ditambahkan. Backup source: $storage_config_backup"
  fi
  storage_base="$app_url/public/storage"
  SIDIKA_STORAGE_BASE="$storage_base" "$php_bin" -r '
$base=getenv("SIDIKA_STORAGE_BASE");
if (!preg_match("~^https?://[a-zA-Z0-9./:_-]+$~D",$base)) { fwrite(STDERR,"URL storage tidak valid.\n"); exit(1); }
$env=file_get_contents(".env");
$line="PUBLIC_STORAGE_URL=".$base;
$count=0;
$updated=preg_replace("/^[ \t]*(?:export[ \t]+)?PUBLIC_STORAGE_URL[ \t]*=[^\r\n]*/m",$line,$env,-1,$count);
if ($count>1) { fwrite(STDERR,"PUBLIC_STORAGE_URL duplikat di .env.\n");exit(1); }
if (!$count) $updated=rtrim($env,"\r\n")."\n".$line."\n";
if (file_put_contents(".env",$updated,LOCK_EX)!==strlen($updated)) exit(1);
' || { restore_storage_config; die 'Gagal menyimpan URL storage. Konfigurasi telah dipulihkan.'; }
  configured_url=$(SIDIKA_PROBE_PATH="$probe_path" "$php_bin" -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo Illuminate\Support\Facades\Storage::disk("public")->url(getenv("SIDIKA_PROBE_PATH"));') || configured_url=''
  storage_valid=1
  storage_failure=''
  if [[ "$configured_url" != "$alternate_probe_url" ]]; then
    storage_valid=0
    storage_failure='URL hasil bootstrap Laravel belum sesuai konfigurasi'
  elif ! probe_ok "$configured_url?sidika_check=$probe_token" "$configured_url.txt?sidika_check=$probe_token"; then
    storage_valid=0
    storage_failure="$probe_reason"
  fi
  for endpoint in / /dinoyo-kamera-way; do
    page_status=$(curl -LsS --max-time 30 --max-redirs 4 -o /dev/null -w '%{http_code}' "$app_url$endpoint?sidika_check=$probe_token") || page_status=000
    if [[ "$page_status" != 200 ]]; then storage_valid=0; storage_failure="$storage_failure; $endpoint HTTP $page_status"; fi
  done
  if [[ "$storage_valid" != 1 ]]; then
    restore_storage_config
    die "Pemeriksaan konfigurasi storage gagal: $storage_failure. .env telah dipulihkan; .htaccess tidak diubah."
  fi
  log 'URL disk public tersimpan dan lolos pemeriksaan foto lokal serta dua halaman publik.'

fi
if [[ "$finish_storage" == 1 ]]; then
  log "Selesai. Storage lokal dan dua halaman publik tervalidasi. Build/migrasi tidak diulang."
else
  log "Selesai. Commit $(git rev-parse --short HEAD): build, migrasi, halaman publik, dan akses foto lokal berhasil diperiksa."
fi
