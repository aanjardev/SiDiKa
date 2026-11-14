<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function test_public_page_renders_the_new_eight_section_structure(): void
    {
        $response = $this->get('/dinoyo-kamera-way')->assertOk();

        $response->assertSee('THE DINOYO KAMERA WAY')
            ->assertSee('Landasan spiritual')
            ->assertSee('Visi misi')
            ->assertSee('Budaya organisasi')
            ->assertSee('Disiplin sukses')
            ->assertSee('DK Way')
            ->assertSee('Tes Psikotes')
            ->assertSee('Secret potion')
            ->assertSee('Menjadi jaringan toko jual beli kamera second terbesar')
            ->assertSee('Go Best Forecast')
            ->assertSee('Jihad Dhuha')
            ->assertSee(config('dinoyo-way.dk_way.jihad_time'));

        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);
        $sections = $xpath->query('//main[@id="dinoyo-way"]/section[@id]');
        $this->assertSame([
            'filosofi', 'landasan-spiritual', 'visi-misi', 'budaya-organisasi',
            'disiplin-sukses', 'dk-way', 'psikotes-tim', 'secret-potion',
        ], array_map(fn ($node) => $node->getAttribute('id'), iterator_to_array($sections)));
    }

    public function test_old_way_sections_are_removed_and_external_links_are_safe(): void
    {
        $response = $this->get('/dinoyo-kamera-way')->assertOk();

        $response->assertDontSee('Enam pilar SIAPPPP')
            ->assertDontSee('Misi besar kami')
            ->assertDontSee('Janji bersama')
            ->assertSee('href="https://erp.asaconsulting.id/psikotest?uu=Dinoyokamera.com"', false)
            ->assertSee('href="https://quran.nu.or.id/"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_spiritual_verse_and_complete_discipline_content_are_present(): void
    {
        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee(config('dinoyo-way.spiritual.arabic_segments.0'))
            ->assertSee(config('dinoyo-way.spiritual.arabic_segments.1'))
            ->assertSee('Dan laksanakanlah salat, tunaikanlah zakat, dan rukuklah beserta orang yang rukuk.')
            ->assertSee('Meningkatkan ketaqwaan dari waktu ke waktu')
            ->assertSee('Sedekah minimal 10% dari penghasilan')
            ->assertSee('Menciptakan lingkungan kerja terbaik');
    }

    public function test_legacy_database_content_does_not_override_new_public_copy(): void
    {
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'mission' => ['center_label' => 'Misi lama'],
            'values' => ['items' => [['name' => 'SIAPPPP lama']]],
            'culture' => ['manifesto' => [['name' => 'legacy']]],
        ])]);

        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee('THE DINOYO KAMERA WAY')
            ->assertDontSee('Misi lama')
            ->assertDontSee('SIAPPPP lama');
    }

    public function test_allowed_database_copy_overrides_config_defaults(): void
    {
        DB::table('catalog_settings')->update(['dinoyo_way_content' => json_encode([
            'title' => 'Way custom',
            'vision_mission' => ['vision' => 'Visi custom untuk pengujian.'],
            'hero' => ['image' => 'public/catalog/dinoyo-way/fake.jpg'],
            'psychotest' => ['url' => 'https://malicious.example/'],
        ])]);

        $this->get('/dinoyo-kamera-way')->assertOk()
            ->assertSee('Way custom')
            ->assertSee('Visi custom untuk pengujian.');

        $content = app(\App\Services\DinoyoWayContent::class)->get();
        $this->assertSame(config('dinoyo-way.hero.image'), $content['hero']['image']);
        $this->assertSame(config('dinoyo-way.psychotest.url'), $content['psychotest']['url']);
    }

    public function test_vision_and_culture_redesign_has_no_mission_reveal_and_uses_accessible_tabs(): void
    {
        $response = $this->get('/dinoyo-kamera-way')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML(mb_convert_encoding($response->getContent(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($document);

        $this->assertSame(7, $xpath->query('//*[@id="visi-misi"]//article[contains(@class,"way-mission-card")]')->length);
        $this->assertSame(0, $xpath->query('//*[@id="visi-misi"]//article[contains(@class,"way-reveal")]')->length);
        $this->assertSame(6, $xpath->query('//*[@id="budaya-organisasi"]//*[@role="tab"]')->length);
        $this->assertSame(6, $xpath->query('//*[@id="budaya-organisasi"]//*[@role="tabpanel"]')->length);
        $this->assertSame(0, $xpath->query('//*[@id="budaya-organisasi"]//*[contains(@class,"way-manifesto")]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="budaya-organisasi"]//*[contains(@class,"way-indicator-table")]')->length);
    }

    public function test_admin_way_editor_requires_authentication(): void
    {
        $this->get('/admin/dinoyo-kamera-way')->assertForbidden();
        $this->post('/admin/dinoyo-kamera-way')->assertForbidden();
    }
}
