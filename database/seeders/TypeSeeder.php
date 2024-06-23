<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Determination of total protein in the blood, urine', 'code' => 'BL.7.1'],
            ['name' => 'Determination of total protein in the cerebrospinal fluid (PROT)', 'code' => 'LQ.2.2'],
            ['name' => 'Determination of albumin in the blood', 'code' => 'BL.7.4'],
            ['name' => 'Determination of troponin I and E in the blood', 'code' => 'BL.7.8'],
            ['name' => 'Determination of C-reactive protein in the blood', 'code' => 'BL.7.9.1'],
            ['name' => 'IgG antibodies to rheumatoid factor Fc (RF)', 'code' => 'IM.8.3.1'],
            ['name' => 'Determination of rheumatoid factor', 'code' => 'BL.INT.3'],
            ['name' => 'Determination azotv urea in the blood, urine', 'code' => 'BL.9.1'],
            ['name' => 'Determination of creatinine in the blood, urine', 'code' => 'BL.9.3'],
            ['name' => 'Determination of uric acid in the blood, urine', 'code' => 'BL.9.7'],
            ['name' => 'Determination of total bilirubin in the blood', 'code' => 'BL.10.1.1'],
            ['name' => 'Determination of free (unconjugated) bilirubin in the blood', 'code' => 'BL.10.1.2'],
            ['name' => 'Determination of conjugated bilirubin in the blood', 'code' => 'BL.10.1.3'],
            ['name' => 'Determination of lactate dehydrogenase (LDH) in blood', 'code' => 'BL.11.1.1'],
            ['name' => 'Determination of aspartate aminotransferase (AST) levels', 'code' => 'BL.11.2.1'],
            ['name' => 'Determination of alanine aminotransferase (ALT) in the blood', 'code' => 'BL.11.2.2'],
            ['name' => 'Determination of Gamma-glutamyl transferase (GGT)', 'code' => 'BL.11.2.3'],
            ['name' => 'Determining the level of alkaline phosphatase in the blood', 'code' => 'BL.INT.1'],
            ['name' => 'Determination of alpha-amylase in the blood, urine', 'code' => 'BL.11.4'],
            ['name' => 'Definition (BL.14.2 annalizator) of potassium in the blood', 'code' => 'BL.14.2'],
            ['name' => 'Determination of creatine kinase MB-fraction in the blood', 'code' => 'BL.11.2.5'],
            ['name' => 'Determination of creatine kinase in the blood', 'code' => 'BL.11.2.4'],
            ['name' => 'Determination of glucose in blood, urine (analyzer)', 'code' => 'BL.12.1'],
            ['name' => 'Determination of glucose in the blood kappilyarnoy (rapid method)', 'code' => 'BL.12.1'],
            ['name' => 'Lipid metabolism', 'code' => 'BL.INT.2'],
            ['name' => 'Determination of cholesterol in the blood', 'code' => 'BL.13.2'],
            ['name' => 'Identify common triglycerides', 'code' => 'BL.13.6'],
            ['name' => 'Determination LP high density cholesterol in the blood (alpha-lipoproteins)', 'code' => 'BL.13.7.2a'],
            ['name' => 'Determination LP of very low density cholesterol in the blood (pre-beta lipoproteins)', 'code' => 'BL.13.7.2b'],
            ['name' => 'Determination LP low density cholesterol in the blood (beta lipoproteins)', 'code' => 'BL.13.7.2c'],
            ['name' => 'Determination indicators electrolyte balance (K, Ca +)', 'code' => 'BL.14'],
            ['name' => 'Determination of magnesium in the blood, urine', 'code' => 'BL.14.4'],
            ['name' => 'Determination of iron in the blood', 'code' => 'BL.14.5'],
            ['name' => 'Determination of blood calcium, urine', 'code' => 'BL.15.1'],
            ['name' => 'Determination of phosphorus in the blood, urine', 'code' => 'BL.15.2'],
            ['name' => 'Coagulation', 'code' => 'CG.7'],
            ['name' => 'Determination of non-stabilized blood clotting time', 'code' => 'CG.2.1.1'],
            ['name' => 'Determination of prothrombin (thromboplastin) time in the blood', 'code' => 'CG.2.1.7'],
            ['name' => 'Determination of thrombin time', 'code' => 'CG.2.1.11'],
            ['name' => 'Determination of activated partial thromboplastin time (APTT)', 'code' => 'CG.2.1.2'],
            ['name' => 'Determination of fibrinogen in the blood', 'code' => 'BL.7.9.6'],
            ['name' => 'Determination of fibrin degradation products (D-dimer)', 'code' => 'CG.4.2.6'],
            ['name' => 'The international normalized ratio (INR) INR: International normalized ratio', 'code' => 'CG.6'],
            ['name' => 'Urinalysis', 'code' => 'UR.7'],
            ['name' => 'Sample Zimnitsky', 'code' => 'UR.5'],
            ['name' => 'Bacterioscopy vaginal smear', 'code' => 'MB.1'],
            ['name' => 'Certain cells of the vaginal epithelium in the secretions of female genital mutilation', 'code' => 'GF.1'],
            ['name' => 'Determination of red blood cells in the secretions of female genital mutilation', 'code' => 'GF.2'],
            ['name' => 'Determination of white blood cells in the secretions of female genital mutilation', 'code' => 'GF.2'],
            ['name' => 'Determination of the purity of vaginal discharge', 'code' => 'GF.5'],
            ['name' => 'Determination of blood groups (A1, A2, A3, B)', 'code' => 'IM.10.1.1'],
            ['name' => 'Determining Rh factor', 'code' => 'IM.10.1.2'],
            ['name' => 'Determination of reaction Coombs', 'code' => 'IM.10.3'],
            ['name' => 'Determination of other antigens of red blood cells (including Rhesus phenotyping)', 'code' => 'IM.10.6.2'],
            ['name' => 'Typing erythrocyte antigens (blood group and Rh factor)', 'code' => 'IM.10.1'],
            ['name' => 'Determination of carbohydrate antigen CA 19-9 levels', 'code' => 'IM.18.1.3a'],
            ['name' => 'Determination of carbohydrate antigen CA 125 in the blood', 'code' => 'IM.18.1.3b'],
            ['name' => 'Determination of carbohydrate antigen CA 15-3 levels', 'code' => 'IM.18.1.3c'],
            ['name' => 'Determination of carcinoembryonic antigen CEA in blood', 'code' => 'IM.18.1.2'],
            ['name' => 'Determination of alpha-fetoprotein AFP levels', 'code' => 'IM.18.1.1'],
            ['name' => 'Determination of total prostate-specific antigen levels (PSA)', 'code' => 'IM.18.1.6a'],
            ['name' => 'Determination of free triiodothyronine in the blood (FT3)', 'code' => 'HR.1.1'],
            ['name' => 'Determination of free thyroxine levels (FT4)', 'code' => 'HR.1.2'],
            ['name' => 'Determination of thyroglobulin in the blood', 'code' => 'HR.1.4'],
            ['name' => 'Determination of thyroid stimulating hormone (TSH) in the blood', 'code' => 'HR.3.6'],
            ['name' => 'Antibodies to thyroglobulin', 'code' => 'IM.4.1.1'],
            ['name' => 'Antibodies to thyroid peroxidase', 'code' => 'IM.4.1.2'],
            ['name' => 'Antibodies to the thyroid stimulating hormone receptor (TSH)', 'code' => 'IM.9.3.2'],

        ];

        foreach ($types as $type) {
            \App\Models\Type::create($type);
        }
    }
}
