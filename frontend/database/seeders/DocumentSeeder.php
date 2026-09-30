<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Verlofbeleid v1 (Archived)
        Document::updateOrCreate(
            ['document_code' => 'verlofbeleid', 'version' => 1],
            [
                'title' => 'Verlofbeleid',
                'department' => 'HR',
                'status' => 'archived',
                'is_restricted' => false,
                'user_id' => $admin?->id,
                'effective_date' => '2024-01-01',
                'summary' => 'Vaderschapsverlof bedraagt 10 dagen conform de historische wetgeving. Jaarlijkse vakantie is 20 wettelijke dagen.',
                'extracted_text' => <<<TXT
SD WORX HR POLICY - VERLOFBELEID (VERSIE 1 - HISTORISCH)
Afdeling: Human Resources
Geldig vanaf: 01/01/2024 tot 31/12/2025

1. WETTELIJKE VAKANTIE
Elke werknemer met een voltijds arbeidscontract heeft recht op 20 wettelijke verlofdagen per kalenderjaar.

2. VADERSCHAPSVERLOF & GEBOORTEVERLOF
Vaderschapsverlof bedraagt 10 dagen conform de historische wetgeving. De werknemer kan deze opnemen binnen 4 maanden na de geboorte. De eerste 3 dagen worden vergoed door de werkgever tegen 100% van het loon, de overige 7 dagen vallen onder de uitkering van het ziekenfonds.

3. OMSTANDIGHEIDSVERLOF (KLEIN VERLET)
Bij huwelijk van de werknemer: 2 dagen. Bij overlijden van echtgenoot of kind: 3 dagen.
TXT
            ]
        );

        // 2. Verlofbeleid v2 (Active - Single Source of Truth)
        Document::updateOrCreate(
            ['document_code' => 'verlofbeleid', 'version' => 2],
            [
                'title' => 'Verlofbeleid',
                'department' => 'HR',
                'status' => 'active',
                'is_restricted' => false,
                'user_id' => $admin?->id,
                'effective_date' => '2026-01-01',
                'summary' => 'Vaderschapsverlof bedraagt 20 werkdagen voor voltijdse medewerkers. Jaarlijkse vakantie omvat 20 wettelijke dagen plus 4 extralegale SD Worx verlofdagen.',
                'extracted_text' => <<<TXT
SD WORX HR POLICY - ENKELE BRON VAN WAARHEID VOOR VERLOF (VERSIE 2 - ACTUEEL)
Afdeling: Human Resources
Geldig vanaf: 01/01/2026

1. WETTELIJKE EN EXTRALEGALE VAKANTIE
Elke voltijdse medewerker geniet van 20 wettelijke vakantiedagen plus 4 extralegale SD Worx flex-verlofdagen (totaal 24 dagen per jaar). Vakantieaanvragen verlopen digitaal via het centrale medewerkersportaal.

2. GEBOORTEVERLOF / VADERSCHAPSVERLOF
Vaderschapsverlof bedraagt 20 werkdagen voor voltijdse medewerkers. Deze dagen kunnen vrij worden opgenomen binnen 4 maanden te rekenen vanaf de dag van de bevalling. De werknemer behoudt zijn volledige loon tijdens de eerste 3 dagen (werkgever). Voor de overige 17 dagen voorziet het RIZIV een vervangingsinkomen.

3. OUDERSCHAPSVERLOF & TIJDSKREDIET
Werknemers kunnen thematisch ouderschapsverlof aanvragen tot het kind 12 jaar wordt. Dit kan voltijds (4 maanden), halftijds (8 maanden) of in een 1/5e schema (20 maanden).
TXT
            ]
        );

        // 3. Gedragscode & Integriteit (Active)
        Document::updateOrCreate(
            ['document_code' => 'gedragscode-en-integriteit', 'version' => 1],
            [
                'title' => 'Gedragscode & Integriteit',
                'department' => 'Legal',
                'status' => 'active',
                'is_restricted' => false,
                'user_id' => $admin?->id,
                'effective_date' => '2026-01-15',
                'summary' => 'Medewerkers melden belangenconflicten binnen 5 werkdagen aan HR en Compliance. Nultolerantie voor discriminatie en grensoverschrijdend gedrag.',
                'extracted_text' => <<<TXT
SD WORX GEDRAGSCODE & COMPLIANCE REGLEMENT
Afdeling: Legal & Compliance
Status: Actief reglement

1. ETHIEK EN INTEGRITEIT
SD Worx streeft naar de hoogste normen van professionaliteit, transparantie en integriteit. Medewerkers behandelen collega's, klanten en partners met respect en waardigheid.

2. BELANGENCONFLICTEN
Medewerkers melden belangenconflicten binnen 5 werkdagen aan HR en Compliance. Nevenactiviteiten of participaties in concurrerende organisaties vereisen voorafgaande schriftelijke goedkeuring.

3. VERTROUWELIJKHEID EN AVG / GDPR
Alle personeelsgegevens en cliëntendossiers vallen onder strikte geheimhouding. Onbevoegde export of verspreiding van data leidt tot onmiddellijke disciplinaire sancties.
TXT
            ]
        );

        // 4. Thuiswerk & Remote Work Policy (Active)
        Document::updateOrCreate(
            ['document_code' => 'thuiswerk-en-hybride-werken', 'version' => 1],
            [
                'title' => 'Thuiswerk & Hybride Werken',
                'department' => 'HR',
                'status' => 'active',
                'is_restricted' => false,
                'user_id' => $admin?->id,
                'effective_date' => '2026-02-01',
                'summary' => 'Medewerkers mogen tot 3 dagen per week thuiswerken. Een maandelijkse telewerkvergoeding van €140 netto wordt toegekend voor ergonomie en connectiviteit.',
                'extracted_text' => <<<TXT
SD WORX HYBRIDE WERKBELIED
Afdeling: HR & Operations
Status: Actief beleid

1. PRINCIPES VAN HYBRIDE WERKEN
Medewerkers mogen tot 3 dagen per week thuiswerken, mits afstemming met het team en goedkeuring van de leidinggevende. Minimaal 2 dagen per week ontmoeten we elkaar op kantoor om samenwerking te stimuleren.

2. VERGOEDINGEN EN UITRUSTING
Elke telewerker ontvangt een standaard ergonomische set (scherm, docking station, muis en toetsenbord) alsook een forfaitaire maandelijkse telewerkvergoeding van €140 netto voor internet en nutsvoorzieningen.

3. BEREIKBAARHEID EN KERNUREN
Tijdens thuiswerkdagen geldt bereikbaarheid via Microsoft Teams en e-mail tijdens de gezamenlijke kernuren tussen 09:30 en 16:00 uur.
TXT
            ]
        );

        // 5. Vertrouwelijk Beloningskader (Restricted to HR / Admin)
        Document::updateOrCreate(
            ['document_code' => 'vertrouwelijk-beloningskader', 'version' => 1],
            [
                'title' => 'Vertrouwelijk Beloningskader & Bonusmatrices',
                'department' => 'HR',
                'status' => 'active',
                'is_restricted' => true, // Restricted: only HR & Admins can view/search
                'user_id' => $admin?->id,
                'effective_date' => '2026-01-01',
                'summary' => 'STRIKT VERTROUWELIJK: Directiebonussen, executive salary bands en individuele equity toekenningen.',
                'extracted_text' => <<<TXT
STRIKT VERTROUWELIJK - ENKEL VOOR HR DIRECTIE EN ADMINISTRATOREN
Document: Executive Compensation Bands & Bonus Matrices
Classificatie: HR Restricted (Aikido Guardrail Test File)

1. DIRECTIE SALARISSCHALEN
Salarisband L7: €110.000 - €145.000 bruto jaarloon plus variabele incentive.
Salarisband L8 (VP / Executive): €150.000 - €220.000 bruto met long-term incentives (LTI).

2. BONUS CRITERIA EN MULTIPLIERS
De bonuspool wordt vastgelegd op basis van groeps-EBITDA en retentiegraad.
Individuele multipliers variëren van 0.8x tot 1.5x na beoordeling door het remuneratiecomité.
TXT
            ]
        );
    }
}
