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
        Storage::fake('r2');
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $this->post('/admin/dinoyo-kamera-way', [
            'content' => $this->editableContent(),
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 600),
        ])->assertSessionHasNoErrors()->assertRedirect('/admin/dinoyo-kamera-way');

        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $image = $saved['hero']['image'];
        $this->assertStringStartsWith('catalog/dinoyo-way/', $image);
        Storage::disk('r2')->assertExists($image);
        $this->get('/dinoyo-kamera-way')->assertOk()->assertSee($image);

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

    public function test_silent_r2_write_failure_uses_a_working_local_hero_url(): void
    {
        $r2 = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $r2->allows('exists')->andReturn(false);
        $r2->allows('putFileAs')->andReturn(false);
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

    public function test_failed_local_fallback_does_not_publish_a_missing_image(): void
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
        Storage::fake('r2');
        $this->actingAs(new User(['id' => 1, 'name' => 'Editor', 'role' => 'staff', 'status' => 'active']));
        $fields = ['philosophy' => 'philosophy.image', 'standards' => 'standards.image'];
        for ($i = 0; $i < 6; $i++) $fields['pillar_'.$i] = 'values.items.'.$i.'.image';
        $files = [];
        foreach (array_keys($fields) as $i => $key) $files[$key] = UploadedFile::fake()->image($key.'.jpg', 320 + $i, 240);
        $this->post('/admin/dinoyo-kamera-way', ['content' => $this->editableContent(), 'page_images' => $files])->assertSessionHasNoErrors();
        $saved = json_decode(DB::table('catalog_settings')->value('dinoyo_way_content'), true);
        $public = $this->get('/dinoyo-kamera-way')->assertOk();
        $editor = $this->get('/admin/dinoyo-kamera-way')->assertOk();
        foreach ($fields as $key => $path) {
            $image = \Illuminate\Support\Arr::get($saved, $path);
            $this->assertStringStartsWith('catalog/dinoyo-way/', $image);
            Storage::disk('r2')->assertExists($image);
            $public->assertSee($image);
            $editor->assertSee('page_images['.$key.']', false);
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

}
