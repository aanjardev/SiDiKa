<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DinoyoWayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'app.key' => 'base64:'.base64_encode(str_repeat('w', 32)),
        ]);
        DB::purge('sqlite');

        Schema::create('catalog_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_website');
            $table->string('logo_path')->nullable();
            $table->json('dinoyo_way_content')->nullable();
            $table->timestamps();
        });
        Schema::create('perusahaan_cabang', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
        });
        DB::table('catalog_settings')->insert(['nama_website' => 'Dinoyo Kamera']);
    }

    public function test_guests_can_read_the_way_and_find_it_in_both_navigation_areas(): void
    {
        $response = $this->get('/dinoyo-kamera-way');

        $response->assertOk()
            ->assertSee('THE DINOYO KAMERA WAY')
            ->assertSee('SIAPPPP')
            ->assertSee('SEMANGAT')
            ->assertSee('IBADAH')
            ->assertSee('AMANAH')
            ->assertSee('PROSES')
            ->assertSee('PROGRESS')
            ->assertSee('PROFESIONAL');

        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);

        foreach (['main-header', 'main-footer'] as $id) {
            $links = $xpath->query('//*[@id="'.$id.'"]//a[contains(@href, "/dinoyo-kamera-way")]');
            $this->assertSame(1, $links->length, 'Missing or duplicate Way link in '.$id);
        }
    }

    public function test_local_way_script_keeps_the_public_web_root_and_a_content_version(): void
    {
        $response = $this->get('/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $script = (new \DOMXPath($document))->query('//script[contains(@src,"dinoyo-way.js")]')->item(0);
        $url = $script->getAttribute('src');
        self::assertSame('/js/dinoyo-way.js', parse_url($url, PHP_URL_PATH));
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        self::assertSame(substr(hash_file('sha256', public_path('js/dinoyo-way.js')), 0, 12), $query['v']);
    }

    public function test_production_way_assets_use_the_same_public_directory_as_its_stylesheets(): void
    {
        $this->app->instance('env', 'production');
        $response = $this->get('/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);
        $script = $xpath->query('//script[contains(@src,"dinoyo-way.js")]')->item(0);
        self::assertSame('/public/js/dinoyo-way.js', parse_url($script->getAttribute('src'), PHP_URL_PATH));
        foreach ($xpath->query('//main//img') as $image) {
            self::assertStringStartsWith('/public/', parse_url($image->getAttribute('src'), PHP_URL_PATH));
        }
        foreach ($xpath->query('//link[contains(@href,"css/legacy/")]') as $stylesheet) {
            self::assertStringStartsWith('/public/css/', parse_url($stylesheet->getAttribute('href'), PHP_URL_PATH));
            self::assertNotEmpty(parse_url($stylesheet->getAttribute('href'), PHP_URL_QUERY));
        }
    }

    public function test_draft_policies_remain_identified_as_proposals(): void
    {
        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee('Usulan jalur karier')
            ->assertSee('Contoh bobot penilaian bulanan')
            ->assertSee('Rekomendasi implementasi')
            ->assertSee('Bobot ini adalah usulan awal')
            ->assertSee('bukan menilai tingkat keimanan pribadi seseorang');
    }

    public function test_visual_evaluation_chart_uses_the_saved_weights(): void
    {
        $items = config('dinoyo-way.evaluation.items');
        foreach ($items as &$item) {
            $item['percentage'] = 25;
        }
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'evaluation' => ['items' => $items],
        ])]);
        $response = $this->get('/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $segments = (new \DOMXPath($document))->query('//*[contains(concat(" ", normalize-space(@class), " "), " way-chart-segment ")]');
        $this->assertCount(4, $segments);
        $offsets = [];
        foreach ($segments as $segment) {
            $this->assertSame('25 75', $segment->getAttribute('stroke-dasharray'));
            $offsets[] = $segment->getAttribute('stroke-dashoffset');
        }
        $this->assertSame(['0', '-25', '-50', '-75'], $offsets);
    }

    public function test_philosophy_has_no_extra_cta_and_mission_keeps_its_own_identity(): void
    {
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'mission' => ['center_label' => 'SIAPPPP'],
        ])]);
        $response = $this->get('/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);
        $cta = $xpath->query('//*[@id="filosofi"]//a[@href="#nilai"]');
        $this->assertCount(0, $cta);
        $this->assertCount(0, $xpath->query('//*[@id="filosofi"]//details | //*[@id="filosofi"]//summary'));
        $response->assertDontSee('Tempat bertumbuh bersama.')->assertSee(config('dinoyo-way.philosophy.paragraphs.1'));
        $this->assertCount(3, $xpath->query('//*[@id="filosofi"]//p[@class="way-lead"] | //*[@id="filosofi"]//div[@class="way-philosophy-reading"]/p'));

        $this->assertCount(1, $xpath->query('//*[@id="nilai"]'));
        $mission = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " way-orbit-center ")]/span');
        $this->assertSame('Misi besar', trim($mission->item(0)->textContent));
        $pledge = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " way-pledge-seal ")]/span');
        $this->assertSame('SIAPPPP', trim($pledge->item(0)->textContent));

        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'mission' => ['center_label' => 'Manfaat bersama'],
            'pledge' => ['emblem_label' => 'Tim Dinoyo'],
        ])]);
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('Manfaat bersama')->assertSee('Tim Dinoyo');
    }

    public function test_page_copy_is_escaped_when_rendered(): void
    {
        config(['dinoyo-way.hero.title' => '<script>alert("copy")</script>']);

        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee('<script>alert("copy")</script>')
            ->assertDontSee('<script>alert("copy")</script>', false);
    }

    public function test_guests_cannot_open_or_save_the_editor(): void
    {
        // The application's exception handler renders 403 for unauthenticated requests.
        $this->get('/admin/dinoyo-kamera-way')->assertForbidden();
        $this->post('/admin/dinoyo-kamera-way', ['content' => []])->assertForbidden();
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
    }

    public function test_staff_can_edit_copy_while_preserving_navigation_labels(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $this->get('/admin/dinoyo-kamera-way')->assertOk()->assertSee('Simpan Perubahan');

        $content = $this->editableContent();
        $content['hero']['title'] = 'BERTUMBUH DENGAN AMANAH';
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode(['navigation_label' => 'Budaya Dinoyo'])]);
        $content['values']['items'][2]['behavior'] = 'Jelaskan kondisi setiap unit secara terbuka.';

        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])
            ->assertSessionHasNoErrors()->assertRedirect('/admin/dinoyo-kamera-way');

        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $this->assertSame('BERTUMBUH DENGAN AMANAH', $saved['hero']['title']);
        $this->assertSame('Dinoyo Kamera', DB::table('catalog_settings')->value('nama_website'));

        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee('BERTUMBUH DENGAN AMANAH')
            ->assertSee('Jelaskan kondisi setiap unit secara terbuka.');
        $this->assertSame(2, substr_count($this->get('/dinoyo-kamera-way')->getContent(), 'Budaya Dinoyo'));
    }

    public function test_page_identity_description_is_hidden_locked_and_preserved_when_other_copy_changes(): void
    {
        $description = 'Deskripsi metadata tersimpan.';
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode(['description' => $description])]);
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $response = $this->get('/admin/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);
        self::assertCount(0, $xpath->query('//*[@name="content[description]"]'));
        self::assertCount(1, $xpath->query('//*[@name="content[hero][description]"]'));
        $content = $this->editableContent();
        self::assertArrayNotHasKey('description', $content);
        $content['hero']['title'] = 'Judul tetap bisa diubah';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasNoErrors();
        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        self::assertSame($description, $saved['description']);
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee($description)->assertSee('Judul tetap bisa diubah');
        $content['description'] = 'Input tersembunyi yang disisipkan';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasErrors('content');
        self::assertSame($saved, json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true));
    }

    public function test_invalid_weights_or_injected_anchor_ids_do_not_change_saved_content(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'manager', 'status' => 'active']));
        $content = $this->editableContent();
        $content['evaluation']['items'][0]['percentage'] = 90;
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])
            ->assertSessionHasErrors('content.evaluation.items');
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));

        $content = $this->editableContent();
        $content['navigation'][0]['id'] = 'broken-anchor';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])
            ->assertSessionHasErrors('content');
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
    }

    public function test_saved_copy_is_escaped_and_missing_fields_keep_the_original_fallback(): void
    {
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'hero' => ['title' => '<script>alert("saved")</script>'],
        ])]);

        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee('<script>alert("saved")</script>')
            ->assertDontSee('<script>alert("saved")</script>', false)
            ->assertSee('PROFESIONAL');
    }

    private function editableContent(): array
    {
        return app(\App\Services\DinoyoWayContent::class)->editable(config('dinoyo-way'));
    }

    public function test_uploaded_hero_is_preserved_by_text_edits_and_can_be_reset(): void
    {
        Storage::fake('public');
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $this->post('/admin/dinoyo-kamera-way', [
            'content' => $this->editableContent(),
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 600),
        ])->assertSessionHasNoErrors()->assertRedirect('/admin/dinoyo-kamera-way');

        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $image = $saved['hero']['image'];
        $this->assertStringStartsWith('public/catalog/dinoyo-way/', $image);
        Storage::disk('public')->assertExists(substr($image, 7));
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee(Storage::disk('public')->url(substr($image, 7)));

        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent()])->assertSessionHasNoErrors();
        $this->assertSame($image, json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true)['hero']['image']);

        $this->post('/admin/dinoyo-kamera-way', [
            'content' => $this->editableContent(), 'reset_hero_image' => 1,
        ])->assertSessionHasNoErrors();
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('/mAboutIMG/about.avif');
    }

    public function test_invalid_upload_is_rejected_without_saving_copy(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'manager', 'status' => 'active']));
        $this->post('/admin/dinoyo-kamera-way', [
            'content' => $this->editableContent(),
            'hero_image' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('hero_image');
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
    }

    public function test_migration_preserves_existing_settings_and_supports_rollback(): void
    {
        Schema::table('catalog_settings', fn (Blueprint $table) => $table->dropColumn('dinoyo_way_content'));
        $migration = require database_path('migrations/2026_10_28_000000_add_dinoyo_way_content_to_catalog_settings.php');
        $migration->up();
        $this->assertTrue(Schema::hasColumn('catalog_settings', 'dinoyo_way_content'));
        $this->assertSame('Dinoyo Kamera', DB::table('catalog_settings')->value('nama_website'));
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
        $migration->down();
        $this->assertFalse(Schema::hasColumn('catalog_settings', 'dinoyo_way_content'));
    }

    public function test_hero_upload_uses_local_storage_without_accessing_r2(): void
    {
        $r2 = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $r2->shouldNotReceive('exists');
        $r2->shouldNotReceive('putFileAs');
        $r2->shouldNotReceive('url');
        Storage::set('r2', $r2);
        Storage::fake('public');
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));

        $this->post('/admin/dinoyo-kamera-way', [
            'content' => $this->editableContent(),
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 600),
        ])->assertSessionHasNoErrors()->assertRedirect('/admin/dinoyo-kamera-way');

        $image = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true)['hero']['image'];
        $this->assertStringStartsWith('public/catalog/dinoyo-way/', $image);
        Storage::disk('public')->assertExists(substr($image, 7));
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('/storage/'.substr($image, 7));
    }

    public function test_failed_local_upload_does_not_publish_a_missing_image(): void
    {
        foreach (['r2', 'public'] as $disk) {
            $adapter = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
            $adapter->allows('exists')->andReturn($disk !== 'r2');
            $adapter->allows('putFileAs')->andReturn(false);
            Storage::set($disk, $adapter);
        }
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $content = $this->editableContent();
        $content['hero']['title'] = 'Teks yang belum tersimpan';
        $this->from('/admin/dinoyo-kamera-way')->post('/admin/dinoyo-kamera-way', [
            'content' => $content,
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 600),
        ])->assertRedirect('/admin/dinoyo-kamera-way')
            ->assertSessionHasErrors('hero_image')
            ->assertSessionHasInput('content.hero.title', 'Teks yang belum tersimpan');
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
    }
    public function test_every_editable_text_field_is_saved_and_reaches_the_public_page(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $content = $this->editableContent();
        $markers = [];
        foreach (\Illuminate\Support\Arr::dot($content) as $path => $value) {
            if (!is_string($value) || str_ends_with($path, '_url')) continue;
            $marker = 'Teks dinamis '.sprintf('%03d', count($markers));
            $markers[] = $marker;
            \Illuminate\Support\Arr::set($content, $path, $marker);
        }
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasNoErrors();
        $response = $this->get('/dinoyo-kamera-way')->assertOk();
        foreach ($markers as $marker) $response->assertSee($marker);
    }

    public function test_old_philosophy_copy_is_normalized_without_reviving_removed_fields(): void
    {
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'philosophy' => ['paragraphs' => ['Paragraf yang sudah dihapus', 'Filosofi aktif satu', 'Filosofi aktif dua'], 'read_less' => 'Ringkas lama'],
            'performance' => ['paragraphs' => ['Pengantar aktif', 'Penjelasan dihapus'], 'read_more' => 'Tombol lama'],
        ])]);
        $content = app(\App\Services\DinoyoWayContent::class)->get();
        $this->assertSame(['Filosofi aktif satu', 'Filosofi aktif dua'], $content['philosophy']['paragraphs']);
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('Filosofi aktif satu')->assertSee('Filosofi aktif dua')
            ->assertDontSee('Paragraf yang sudah dihapus')->assertDontSee('Penjelasan dihapus')->assertDontSee('Ringkas lama')->assertDontSee('Tombol lama');
    }

    public function test_all_editorial_photos_can_be_uploaded_preserved_and_reset_independently(): void
    {
        Storage::fake('public');
        $r2 = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $r2->shouldNotReceive('exists');
        $r2->shouldNotReceive('putFileAs');
        $r2->shouldNotReceive('url');
        Storage::set('r2', $r2);
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $fields = ['philosophy' => 'philosophy.image', 'standards' => 'standards.image'];
        for ($i = 0; $i < 6; $i++) $fields['pillar_'.$i] = 'values.items.'.$i.'.image';
        $files = [];
        foreach (array_keys($fields) as $i => $key) $files[$key] = UploadedFile::fake()->image($key.'.jpg', 320 + $i, 240);
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent(), 'page_images' => $files])->assertSessionHasNoErrors();
        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $public = $this->get('/dinoyo-kamera-way')->assertOk();
        $editor = $this->get('/admin/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($public->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);
        foreach ($fields as $key => $path) {
            $image = \Illuminate\Support\Arr::get($saved, $path);
            $this->assertStringStartsWith('public/catalog/dinoyo-way/', $image);
            Storage::disk('public')->assertExists(substr($image, 7));
            $public->assertSee(Storage::disk('public')->url(substr($image, 7)));
            $editor->assertSee('page_images['.$key.']', false);
            $selector = match ($key) {
                'philosophy' => '//*[@id="filosofi"]//figure/img',
                'standards' => '//*[@id="cara-kerja"]//figure/img',
                default => '//*[@id="way-value-panel-'.substr($key, 7).'"]//img',
            };
            $nodes = $xpath->query($selector);
            $this->assertCount(1, $nodes);
            $this->assertSame(Storage::disk('public')->url(substr($image, 7)), $nodes->item(0)->getAttribute('src'));
            $this->assertSame('image/webp', getimagesize(Storage::disk('public')->path(substr($image, 7)))['mime']);
        }
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent()])->assertSessionHasNoErrors();
        $this->assertSame($saved, json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true));
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent(), 'reset_page_images' => ['pillar_0' => 1]])->assertSessionHasNoErrors();
        $after = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $this->assertSame(config('dinoyo-way.values.items.0.image'), $after['values']['items'][0]['image']);
        $this->assertSame($saved['values']['items'][1]['image'], $after['values']['items'][1]['image']);
    }

    public function test_editor_rejects_bad_photos_paths_and_locked_cta_changes_without_saving(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent(), 'page_images' => ['pillar_0' => UploadedFile::fake()->create('fake.pdf', 10, 'application/pdf')]])->assertSessionHasErrors('page_images.pillar_0');
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent(), 'page_images' => ['unknown' => UploadedFile::fake()->image('unknown.jpg')]])->assertSessionHasErrors('page_images');
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent(), 'page_images' => ['philosophy' => UploadedFile::fake()->image('photo.jpg')], 'reset_page_images' => ['philosophy' => 1]])->assertSessionHasErrors('page_images.philosophy');
        foreach (['javascript:alert(1)', '//example.com', '/\\example.com', 'data:text/html,test', ['invalid-array']] as $link) {
            $content = $this->editableContent();
            $content['closing']['secondary_action_url'] = $link;
            $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasErrors('content.closing');
        }
        $content = $this->editableContent();
        $content['values']['items'][0]['image'] = 'https://example.com/untrusted.jpg';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasErrors('content.values.items.0');
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
    }

    public function test_failed_editorial_photo_upload_preserves_copy_and_published_settings(): void
    {
        foreach (['r2', 'public'] as $disk) {
            $adapter = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
            $adapter->allows('exists')->andReturn($disk !== 'r2');
            $adapter->allows('putFileAs')->andReturn(false);
            Storage::set($disk, $adapter);
        }
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $content = $this->editableContent();
        $content['closing']['title'] = 'Judul baru yang belum disimpan';
        $this->from('/admin/dinoyo-kamera-way')->post('/admin/dinoyo-kamera-way', [
            'content' => $content,
            'page_images' => ['pillar_0' => UploadedFile::fake()->image('pillar.jpg', 320, 240)],
        ])->assertRedirect('/admin/dinoyo-kamera-way')->assertSessionHasErrors('page_images.pillar_0')
            ->assertSessionHasInput('content.closing.title', 'Judul baru yang belum disimpan');
        $this->assertNull(DB::table('catalog_settings')->value('dinoyo_way_content'));
    }

    public function test_primary_button_settings_are_hidden_locked_and_preserved_by_content_edits(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'hero' => ['action' => 'Nilai bersama', 'action_url' => '#nilai'],
            'closing' => ['action' => 'Mari berbincang', 'action_url' => '/contact?source=way'],
        ])]);
        $editor = $this->get('/admin/dinoyo-kamera-way')->assertOk();
        foreach (['hero', 'closing'] as $section) {
            foreach (['action', 'action_url'] as $field) {
                $editor->assertDontSee('name="content['.$section.']['.$field.']"', false);
            }
        }
        $content = $this->editableContent();
        $content['closing']['title'] = 'Judul penutup diperbarui';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasNoErrors();
        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $this->assertSame('Nilai bersama', $saved['hero']['action']);
        $this->assertSame('#nilai', $saved['hero']['action_url']);
        $this->assertSame('Mari berbincang', $saved['closing']['action']);
        $this->assertSame('/contact?source=way', $saved['closing']['action_url']);
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('Judul penutup diperbarui')
            ->assertSee('Mari berbincang')->assertSee('/contact?source=way');
        $content['closing']['action_url'] = '/tautan-tidak-diizinkan';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasErrors('content.closing');
        $this->assertSame($saved, json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true));
    }

    public function test_link_and_image_alt_settings_are_not_editable_but_keep_their_saved_values(): void
    {
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $content = $this->editableContent();
        $locked = ['navigation_label', 'navigation_aria_label', 'navigation', 'hero.secondary_action', 'hero.secondary_action_url', 'closing.secondary_action', 'closing.secondary_action_url', 'hero.image_alt', 'philosophy.image_alt', 'standards.image_alt'];
        for ($i = 0; $i < 6; $i++) $locked[] = 'values.items.'.$i.'.image_alt';
        foreach ($locked as $path) $this->assertFalse(\Illuminate\Support\Arr::has($content, $path), $path.' should not be editable');
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'hero' => ['secondary_action' => 'Tautan tersimpan', 'secondary_action_url' => '#cara-kerja', 'image_alt' => 'Deskripsi hero tersimpan'],
            'closing' => ['secondary_action' => 'Katalog tersimpan', 'secondary_action_url' => '/katalog?source=way'],
            'values' => ['items' => [['image_alt' => 'Deskripsi foto pilar tersimpan']]],
        ])]);
        $content['hero']['title'] = 'Judul tetap dapat diubah';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasNoErrors();
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('Judul tetap dapat diubah')
            ->assertSee('Tautan tersimpan')->assertSee('Katalog tersimpan')->assertSee('/katalog?source=way')
            ->assertSee('Deskripsi hero tersimpan')->assertSee('Deskripsi foto pilar tersimpan');
        $editor = $this->get('/admin/dinoyo-kamera-way')->assertOk();
        $editor->assertDontSee('Teks alternatif foto')->assertDontSee('Tujuan tautan tambahan')->assertDontSee('Teks tautan tambahan');
        $content['hero']['image_alt'] = 'Tidak boleh diubah';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content])->assertSessionHasErrors('content.hero');
    }

    public function test_replacing_a_photo_and_text_changes_the_public_image_src_and_keeps_other_photos(): void
    {
        Storage::fake('public');
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $content = $this->editableContent();
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content, 'page_images' => [
            'pillar_0' => UploadedFile::fake()->image('before.jpg', 320, 240),
            'pillar_1' => UploadedFile::fake()->image('retained.jpg', 400, 300),
        ]])->assertSessionHasNoErrors();
        $before = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $content['closing']['title'] = 'Teks dan foto versi kedua';
        $this->post('/admin/dinoyo-kamera-way', ['content' => $content, 'page_images' => [
            'pillar_0' => UploadedFile::fake()->image('after.jpg', 420, 280),
        ]])->assertSessionHasNoErrors();
        $after = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $this->assertNotSame($before['values']['items'][0]['image'], $after['values']['items'][0]['image']);
        $this->assertSame($before['values']['items'][1]['image'], $after['values']['items'][1]['image']);
        $response = $this->get('/dinoyo-kamera-way')->assertOk()->assertSee('Teks dan foto versi kedua');
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $photo = (new \DOMXPath($document))->query('//*[@id="way-value-panel-0"]//img')->item(0);
        $this->assertSame(Storage::disk('public')->url(substr($after['values']['items'][0]['image'], 7)), $photo->getAttribute('src'));
        $this->assertStringNotContainsString(basename($before['values']['items'][0]['image']), $photo->getAttribute('src'));
    }

    public function test_public_storage_url_can_match_a_shared_hosting_public_directory(): void
    {
        $previous = $_ENV['PUBLIC_STORAGE_URL'] ?? null;
        $_ENV['PUBLIC_STORAGE_URL'] = 'https://dinoyokamera.com/public/storage';
        try {
            $filesystems = require config_path('filesystems.php');
            self::assertSame('https://dinoyokamera.com/public/storage', $filesystems['disks']['public']['url']);
            Storage::fake('public', ['url' => $filesystems['disks']['public']['url']]);
            $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
            $this->post('/admin/dinoyo-kamera-way', [
                'content' => $this->editableContent(),
                'page_images' => ['pillar_0' => UploadedFile::fake()->image('hosting.jpg', 320, 240)],
            ])->assertSessionHasNoErrors();
            $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
            $path = substr($saved['values']['items'][0]['image'], 7);
            Storage::disk('public')->assertExists($path);
            $url = 'https://dinoyokamera.com/public/storage/'.$path;
            $this->get('/dinoyo-kamera-way')->assertOk()->assertSee($url);
            $this->get('/admin/dinoyo-kamera-way')->assertOk()->assertSee($url);
        } finally {
            if ($previous === null) unset($_ENV['PUBLIC_STORAGE_URL']);
            else $_ENV['PUBLIC_STORAGE_URL'] = $previous;
        }
    }

    public function test_other_uploads_keep_the_existing_default_r2_behavior(): void
    {
        Storage::fake('public');
        Storage::fake('r2');
        $result = \App\Helpers\ImageUpload::upload(UploadedFile::fake()->image('product.jpg', 320, 240), 'catalog/products');
        $this->assertStringStartsWith('catalog/products/', $result['path']);
        Storage::disk('r2')->assertExists($result['path']);
        Storage::disk('public')->assertMissing($result['path']);
    }

}
