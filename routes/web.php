<?php

use App\Http\Controllers\admin\CalculationRuleCT;
use App\Http\Controllers\admin\CategoryCT;
use App\Http\Controllers\admin\CompatibilityMaterialCT;
use App\Http\Controllers\admin\DashboardCT;
use App\Http\Controllers\admin\DiscountCT;
use App\Http\Controllers\admin\DisplayCT;
use App\Http\Controllers\admin\DisplayPricingRuleCT;
use App\Http\Controllers\admin\EngineCT;
use App\Http\Controllers\admin\CuttingCT;
use App\Http\Controllers\admin\EngineRateCT;
use App\Http\Controllers\admin\EstimationCT;
use App\Http\Controllers\admin\EstimatorCT;
use App\Http\Controllers\admin\FinishingServiceCT;
use App\Http\Controllers\admin\GallerySampleCT;
use App\Http\Controllers\admin\LaminationCT;
use App\Http\Controllers\admin\LaminationPricingRuleCT;
use App\Http\Controllers\admin\LocationCT;
use App\Http\Controllers\admin\MaterialCT;
use App\Http\Controllers\admin\MaterialPriceCT;
use App\Http\Controllers\admin\MaterialSizeCT;
use App\Http\Controllers\admin\OffsetEstimationCT;
use App\Http\Controllers\admin\OffsetMasterCT;
use App\Http\Controllers\admin\PricingCT;
use App\Http\Controllers\admin\PricingPolicyCT;
use App\Http\Controllers\admin\PricingRulesCT;
use App\Http\Controllers\admin\ProductionCostCT;
use App\Http\Controllers\admin\ProductionCostFinishingCuttingCT;
use App\Http\Controllers\admin\ProductionCostFinishingCT;
use App\Http\Controllers\admin\ProductionCostFinishingDisplayCT;
use App\Http\Controllers\admin\ProductionCostFinishingLaminationCT;
use App\Http\Controllers\admin\VendorCT;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GetDataCT;
use App\Http\Controllers\user\CatalogCT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// user
Route::get('/', [CatalogCT::class, 'index'])->name('user_home');
Route::get('/user/products/data', [CatalogCT::class, 'data'])->name('user_products_data');
Route::get('/product-details', [CatalogCT::class, 'productDetails'])->name('user_product_details');
Route::get('/products', [CatalogCT::class, 'products'])->name('user_products');
Route::get('/baskets', [CatalogCT::class, 'baskets'])->name('user_baskets');
Route::get('/user/images/display/{id}', [CatalogCT::class, 'displayImage'])->name('user_display_image');
Route::get('/user/images/gallery/{id}', [CatalogCT::class, 'galleryImage'])->name('user_gallery_image');
Route::get('/user/products/filters', [CatalogCT::class, 'filters'])->name('user_products_filters');
Route::get('/user/products/list', [CatalogCT::class, 'productsList'])->name('user_products_list');
Route::post('/estimation/preview-lf', [CatalogCT::class, 'previewUserLFEstimation'])->name('user_preview_lf_estimation');

Route::get('/estimation/create-lf/get-materials', [CatalogCT::class, 'getUserMaterials'])->name('user_get_materials');

Route::get('/estimation/create-lf/get-material-sizes', [CatalogCT::class, 'getUserMaterialSizes'])->name('user_get_material_sizes');

Route::get('/estimation/create-lf/get-laminations', [CatalogCT::class, 'getUserLaminations'])->name('user_get_laminations');

Route::get('/estimation/create-lf/get-lamination-sizes', [CatalogCT::class, 'getUserLaminationSizes'])->name('user_get_laminations_sizes');

Route::get('/estimation/create-lf/get-categories', [CatalogCT::class, 'getUserLFCategories'])->name('user_get_lf_categories');

Route::post('/estimation/preview-a3plus', [CatalogCT::class, 'previewUserA3PlusEstimation'])->name('user_preview_a3plus_estimation');
Route::get('/estimation/create-a3plus/get-materials', [CatalogCT::class, 'getUserMaterialsA3plus'])->name('user_get_materials_a3plus');
Route::post('/estimation/preview-brosur', [CatalogCT::class, 'previewUserBrosurEstimation'])->name('user_preview_brosur_estimation');

// Route::get('/user/estimator/materials', [CatalogCT::class, 'estimatorMaterials'])
//     ->middleware('throttle:60,1')->name('user_estimator_materials');
// Route::get('/user/estimator/material-sizes', [CatalogCT::class, 'estimatorMaterialSizes'])
//     ->middleware('throttle:60,1')->name('user_estimator_material_sizes');
// Route::get('/user/estimator/laminations', [CatalogCT::class, 'estimatorLaminations'])
//     ->middleware('throttle:60,1')->name('user_estimator_laminations');
// Route::get('/user/estimator/lamination-sizes', [CatalogCT::class, 'estimatorLaminationSizes'])
//     ->middleware('throttle:60,1')->name('user_estimator_lamination_sizes');


// admin
Route::get('/admin/signin', function () {
    if (Auth::check()) {
        return redirect()->route('admin_home');
    }

    return view('admin/signin');
})->name('admin_signin');

Route::post('/admin/signin', [AuthController::class, 'login'])->name('admin_signin_process');

Route::middleware('auth')->group(function () {

    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin_logout');

    Route::get('/admin', [DashboardCT::class, 'index'])->name('admin_home');


    // kategori
    Route::get('/admin/categories', [CategoryCT::class, 'index'])->name('admin_categories');
    Route::get('/admin/categories/data', [CategoryCT::class, 'data'])->name('admin_categories_data');
    Route::post('/admin/categories/store', [CategoryCT::class, 'store'])->name('admin_store_categories');
    Route::delete('/admin/categories/{id}', [CategoryCT::class, 'destroy'])->name('admin_destroy_category');
    Route::put('/admin/categories/{id}', [CategoryCT::class, 'update'])->name('admin_update_category');
    Route::get('/admin/categories/export', [CategoryCT::class, 'export'])->name('admin_export_category');
    Route::post('/admin/categories/import', [CategoryCT::class, 'import'])->name('admin_import_category');
    Route::get('/admin/categories/template', [CategoryCT::class, 'downloadTemplate'])->name('admin_download_category_template');

    Route::post('/admin/engines/create', [EngineCT::class, 'store'])->name('admin_store_engine');
    Route::put('/admin/engines/{id}/edit', [EngineCT::class, 'update'])->name('admin_update_engine');
    Route::get('/admin/engines', [EngineCT::class, 'index'])->name('admin_engines');
    Route::get('/admin/engines/data', [EngineCT::class, 'data'])->name('admin_data_engine');
    Route::post('/admin/engines/import', [EngineCT::class, 'import'])->name('admin_import_engine');
    Route::delete('/admin/engines/{id}/delete', [EngineCT::class, 'destroy'])->name('admin_destroy_engine');
    Route::get('/admin/engines/export', [EngineCT::class, 'export'])->name('admin_export_engine');

    Route::get('/admin/vendors', [VendorCT::class, 'index'])->name('admin_vendors');
    Route::post('/admin/vendors/create', [VendorCT::class, 'store'])->name('admin_store_vendor');
    Route::get('/admin/vendors/{id}/edit', [VendorCT::class, 'edit'])->name('admin_edit_vendor');
    Route::put('/admin/vendors/{id}/update', [VendorCT::class, 'update'])->name('admin_update_vendor');
    Route::get('/admin/vendors/data', [VendorCT::class, 'data'])->name('admin_data_vendor');
    Route::delete('/admin/vendors/{id}/delete', [VendorCT::class, 'destroy'])->name('admin_destroy_vendor');
    Route::get('/admin/vendors/export', [VendorCT::class, 'export'])->name('admin_export_vendor');
    Route::post('/admin/vendors/import', [VendorCT::class, 'import'])->name('admin_import_vendor');

    Route::get('/admin/engine-rates', [EngineRateCT::class, 'index'])->name('admin_engine_rates');
    Route::get('/admin/engine-rates/create', [EngineRateCT::class, 'create'])->name('admin_create_engine_rate');
    Route::get('/admin/engine-rates/edit', [EngineRateCT::class, 'edit'])->name('admin_edit_engine_rate');

    Route::get('/admin/finishing-services', [FinishingServiceCT::class, 'index'])->name('admin_finishing_services');
    Route::get('/admin/finishing-services/create', [FinishingServiceCT::class, 'create'])->name('admin_create_finishing_service');
    Route::get('/admin/finishing-services/edit', [FinishingServiceCT::class, 'edit'])->name('admin_edit_finishing_service');

    Route::get('/admin/materials', [MaterialCT::class, 'index'])->name('admin_materials');
    Route::post('/admin/materials/store', [MaterialCT::class, 'store'])->name('admin_store_material');
    Route::get('/admin/materials/{id}/edit', [MaterialCT::class, 'edit'])->name('admin_edit_material');
    Route::put('/admin/materials/{id}/update', [MaterialCT::class, 'update'])->name('admin_update_material');
    Route::get('/admin/materials/data', [MaterialCT::class, 'data'])->name('admin_data_material');
    Route::post('/admin/materials/import', [MaterialCT::class, 'import'])->name('admin_import_material');
    Route::get('/admin/materials/export', [MaterialCT::class, 'export'])->name('admin_export_material');
    Route::delete('/admin/materials/{id}/delete', [MaterialCT::class, 'destroy'])->name('admin_destroy_material');
    Route::get('/admin/materials/{id}/details', [MaterialCT::class, 'details'])->name('admin_material_details');

    Route::get('/admin/pricings', [PricingCT::class, 'index'])->name('admin_pricings');
    Route::get('/admin/pricings/create', [PricingCT::class, 'create'])->name('admin_create_pricing');
    Route::get('/admin/pricings/edit', [PricingCT::class, 'edit'])->name('admin_edit_pricing');

    Route::get('/admin/pricing-policies', [PricingPolicyCT::class, 'index'])->name('admin_pricing_policies');
    Route::get('/admin/pricing-policies/create', [PricingPolicyCT::class, 'create'])->name('admin_create_pricing_policy');
    Route::get('/admin/pricing-policies/edit', [PricingPolicyCT::class, 'edit'])->name('admin_edit_pricing_policy');


    Route::get('/admin/calculation-rules', [CalculationRuleCT::class, 'index'])->name('admin_calculation_rules');
    Route::get('/admin/calculation-rules/create', [CalculationRuleCT::class, 'create'])->name('admin_create_calculation_rule');
    Route::get('/admin/calculation-rules/{id}/edit', [CalculationRuleCT::class, 'edit'])->name('admin_edit_calculation_rule');
    Route::put('/admin/calculation-rules/{id}/edit', [CalculationRuleCT::class, 'update'])->name('admin_update_calculation_rule');
    Route::post('/admin/calculation-rules/create', [CalculationRuleCT::class, 'store'])->name('admin_store_calculation_rule');
    Route::get('/admin/calculation-rules/data', [CalculationRuleCT::class, 'data'])->name('admin_data_calculation_rules');
    Route::post('/admin/calculation-rules/import', [CalculationRuleCT::class, 'import'])->name('admin_import_calculation_rule');
    Route::get('/admin/calculation-rules/export', [CalculationRuleCT::class, 'export'])->name('admin_export_calculation_rule');
    Route::delete('/admin/calculation-rules/{id}/delete', [CalculationRuleCT::class, 'destroy'])->name('admin_delete_calculation_rules');
    Route::get('/admin/calculation-rules/{id}/details', [CalculationRuleCT::class, 'details'])->name('admin_calculation_rule_details');

    Route::get('/admin/compatibility-material', [CompatibilityMaterialCT::class, 'index'])->name('admin_compatibility_materials');
    Route::get('/admin/compatibility-material/data', [CompatibilityMaterialCT::class, 'data'])->name('admin_compatibility_materials_data');
    Route::get('/admin/compatibility-material/create', [CompatibilityMaterialCT::class, 'create'])->name('admin_create_compatibility_material');
    Route::post('/admin/compatibility-material/store', [CompatibilityMaterialCT::class, 'store'])->name('admin_store_compatibility_material');
    Route::get('/admin/compatibility-material/{id}/edit', [CompatibilityMaterialCT::class, 'edit'])->name('admin_edit_compatibility_material');
    Route::put('/admin/compatibility-material/{id}/update', [CompatibilityMaterialCT::class, 'update'])->name('admin_update_compatibility_material');
    Route::delete('/admin/compatibility-material/{id}/delete', [CompatibilityMaterialCT::class, 'destroy'])->name('admin_destroy_compatibility_material');
    Route::get('/admin/compatibility-material/{id}/details', [CompatibilityMaterialCT::class, 'details'])->name('admin_compatibility_material_details');

    Route::get('/admin/locations', [LocationCT::class, 'index'])->name('admin_locations');
    Route::get('/admin/locations/data', [LocationCT::class, 'data'])->name('admin_data_locations');
    Route::post('/admin/locations/store', [LocationCT::class, 'store'])->name('admin_store_location');
    Route::post('/admin/locations/import', [LocationCT::class, 'import'])->name('admin_import_location');
    Route::delete('/admin/locations/{id}/delete', [LocationCT::class, 'destroy'])->name('admin_delete_location');
    Route::get('/admin/locations/exports', [LocationCT::class, 'export'])->name('admin_export_location');
    Route::put('/admin/locations/{id}/edit', [LocationCT::class, 'update'])->name('admin_update_location');

    Route::get('/admin/material-sizes', [MaterialSizeCT::class, 'index'])->name('admin_material_sizes');
    Route::get('/admin/material-sizes/create', [MaterialSizeCT::class, 'create'])->name('admin_create_material_size');
    Route::post('/admin/material-sizes/create', [MaterialSizeCT::class, 'store'])->name('admin_store_material_size');
    Route::get('/admin/material-sizes/data', [MaterialSizeCT::class, 'data'])->name('admin_data_material_size');
    Route::get('/admin/material-sizes/export', [MaterialSizeCT::class, 'export'])->name('admin_export_material_size');
    Route::get('/admin/material-sizes/{id}/details', [MaterialSizeCT::class, 'details'])->name('admin_material_size_details');
    Route::delete('/admin/material-sizes/{id}/delete', [MaterialSizeCT::class, 'destroy'])->name('admin_destroy_material_size');
    Route::get('/admin/material-sizes/{id}/edit', [MaterialSizeCT::class, 'edit'])->name('admin_edit_material_size');
    Route::put('/admin/material-sizes/{id}/edit', [MaterialSizeCT::class, 'update'])->name('admin_update_material_size');
    Route::post('/admin/material-sizes/import', [MaterialSizeCT::class, 'import'])->name('admin_import_material_size');

    Route::get('/admin/production-costs', [ProductionCostCT::class, 'index'])->name('admin_production_costs');
    Route::get('/admin/production-costs/create', [ProductionCostCT::class, 'create'])->name('admin_create_production_cost');
    Route::post('/admin/production-costs/create', [ProductionCostCT::class, 'store'])->name('admin_store_production_cost');
    Route::get('/admin/production-costs/data', [ProductionCostCT::class, 'data'])->name('admin_data_production_cost');
    Route::get('/admin/production-costs/{id}/edit', [ProductionCostCT::class, 'edit'])->name('admin_edit_production_cost');
    Route::put('/admin/production-costs/{id}/edit', [ProductionCostCT::class, 'update'])->name('admin_update_production_cost');
    Route::delete('/admin/production-costs/{id}/delete', [ProductionCostCT::class, 'destroy'])->name('admin_delete_production_cost');
    Route::get('/admin/production-costs/{id}/details', [ProductionCostCT::class, 'details'])->name('admin_production_cost_details');
    Route::post('/admin/production-costs/import', [ProductionCostCT::class, 'import'])->name('admin_import_production_cost');
    Route::get('/admin/production-costs/export', [ProductionCostCT::class, 'export'])->name('admin_export_production_cost');

    Route::get('/admin/material-prices', [MaterialPriceCT::class, 'index'])->name('admin_material_prices');
    Route::get('/admin/material-prices/create', [MaterialPriceCT::class, 'create'])->name('admin_create_material_price');
    Route::post('/admin/material-prices/create', [MaterialPriceCT::class, 'store'])->name('admin_store_material_price');
    Route::get('/admin/material-prices/data', [MaterialPriceCT::class, 'data'])->name('admin_data_material_prices');
    Route::delete('/admin/material-prices/{id}/delete', [MaterialPriceCT::class, 'destroy'])->name('admin_delete_material_price');
    Route::get('/admin/material-prices/{id}/edit', [MaterialPriceCT::class, 'edit'])->name('admin_edit_material_price');
    Route::put('/admin/material-prices/{id}/update', [MaterialPriceCT::class, 'update'])->name('admin_update_material_price');
    Route::post('/admin/material-prices/import', [MaterialPriceCT::class, 'import'])->name('admin_import_material_price');
    Route::get('/admin/material-prices/export', [MaterialPriceCT::class, 'export'])->name('admin_export_material_price');

    Route::get('/admin/estimations', [EstimationCT::class, 'index'])->name('admin_estimations');
    Route::get('/admin/estimations/create-lf', [EstimationCT::class, 'createLF'])->name('admin_create_lf_estimation');
    Route::get('/admin/estimation/create-lf/get-materials', [EstimationCT::class, 'getMaterials'])->name('admin_get_materials');
    Route::post('/admin/estimation/create-lf', [EstimationCT::class, 'storeLF'])->name('admin_store_lf');
    Route::get('/admin/estimations/create-a3plus', [EstimationCT::class, 'createA3plus'])->name('admin_create_a3plus_estimation');
    Route::get('/admin/estimation/create-a3plus/get-materials', [EstimationCT::class, 'getMaterialsA3plus'])->name('admin_get_materials_a3plus');
    Route::post('/admin/estimation/create-a3plus', [EstimationCT::class, 'storeA3plus'])->name('admin_store_a3plus');

    // Cetak Offset Estimation
    Route::get('/admin/estimations/create-offset', [OffsetEstimationCT::class, 'create'])->name('admin_create_offset_estimation');
    Route::post('/admin/estimations/calculate-offset', [OffsetEstimationCT::class, 'calculate'])->name('admin_calculate_offset');
    Route::post('/admin/estimations/store-offset', [OffsetEstimationCT::class, 'store'])->name('admin_store_offset');
    Route::get('/admin/estimation/create-lf/get-material-sizes', [EstimationCT::class, 'getMaterialSizes'])->name('admin_get_material_sizes');
    Route::get('/admin/estimation/create-lf/get-laminations', [EstimationCT::class, 'getLaminations'])->name('admin_get_laminations');
    Route::get('/admin/estimation/create-lf/get-lamination-sizes', [EstimationCT::class, 'getLaminationSizes'])->name('admin_get_laminations_sizes');
    Route::get('/admin/estimation/create-lf/get-categories', [EstimationCT::class, 'getLFCategories'])->name('admin_get_lf_categories');

    Route::get('/admin/production-cost-finishings', [ProductionCostFinishingCT::class, 'index'])->name('admin_pc_finishings');
    Route::get('/admin/production-cost-finishings/create', [ProductionCostFinishingCT::class, 'create'])->name('admin_create_pc_finishing');
    Route::get('/get-material-sizes', [GetDataCT::class, 'getMaterialSizes'])->name('get_material_sizes');
    Route::get('/get-lamination-sizes', [GetDataCT::class, 'getLaminationSizes'])->name('get_lamination_sizes');
    Route::post('/admin/production-cost-finishings/create', [ProductionCostFinishingCT::class, 'store'])->name('admin_store_pc_finishing');
    Route::get('/admin/production-cost-finishings/data', [ProductionCostFinishingCT::class, 'data'])->name('admin_data_pc_finishings');
    Route::delete('/admin/production-cost-finishings/{id}/delete', [ProductionCostFinishingCT::class, 'destroy'])->name('admin_delete_pc_finishing');
    Route::get('/admin/production-cost-finishings/{id}/edit', [ProductionCostFinishingCT::class, 'edit'])->name('admin_edit_pc_finishing');
    Route::put('/admin/production-cost-finishings/{id}/edit', [ProductionCostFinishingCT::class, 'update'])->name('admin_update_pc_finishing');
    Route::get('/admin/production-cost-finishings/{id}/details', [ProductionCostFinishingCT::class, 'details'])->name('admin_pc_finishing_details');
    Route::get('/admin/production-cost-finishings/digital/{id}/details', [ProductionCostFinishingCT::class, 'detailsDigital'])->name('admin_pc_finishing_digital_details');
    Route::get('/admin/production-cost-finishings/digital/{id}/edit', [ProductionCostFinishingCT::class, 'editDigital'])->name('admin_edit_pc_finishing_digital');
    Route::put('/admin/production-cost-finishings/digital/{id}/edit', [ProductionCostFinishingCT::class, 'updateDigital'])->name('admin_update_pc_finishing_digital');
    Route::delete('/admin/production-cost-finishings/digital/{id}/delete', [ProductionCostFinishingCT::class, 'destroyDigital'])->name('admin_delete_pc_finishing_digital');
    Route::get('/admin/production-cost-finishings/export', [ProductionCostFinishingCT::class, 'export'])->name('admin_export_pc_finishings');
    Route::post('/admin/production-cost-finishing/import', [ProductionCostFinishingCT::class, 'import'])->name('admin_import_pc_finishing');

    Route::get('/admin/pricing-rules', [PricingRulesCT::class, 'index'])->name('admin_pricing_rules');
    Route::get('/admin/pricing-rules/data', [PricingRulesCT::class, 'data'])->name('admin_data_pricing_rule');
    Route::post('/admin/pricing-rules/create', [PricingRulesCT::class, 'store'])->name('admin_store_pricing_rule');
    Route::post('/admin/pricing-rules/digital/create', [PricingRulesCT::class, 'storeDigital'])->name('admin_store_digital_pricing_rule');
    Route::get('/admin/pricing-rules/digital/{id}/details', [PricingRulesCT::class, 'detailsDigital'])->name('admin_digital_pricing_rule_details');
    Route::put('/admin/pricing-rules/digital/{id}', [PricingRulesCT::class, 'updateDigital'])->name('admin_update_digital_pricing_rule');
    Route::delete('/admin/pricing-rules/digital/{id}', [PricingRulesCT::class, 'destroyDigital'])->name('admin_delete_digital_pricing_rule');
    Route::get('/admin/pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/edit', [PricingRulesCT::class, 'edit'])->name('admin_edit_pricing_rule');
    Route::put('/admin/pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/edit', [PricingRulesCT::class, 'update'])->name('admin_update_pricing_rule');
    Route::get('/admin/pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/details', [PricingRulesCT::class, 'details'])->name('admin_pricing_rule_details');
    Route::delete('/admin/pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/delete', [PricingRulesCT::class, 'destroy'])->name('admin_delete_pricing_rule');
    Route::get('/admin/pricing-rules/export', [PricingRulesCT::class, 'export'])->name('admin_export_pricing_rules');
    Route::post('/admin/pricing-rules/import', [PricingRulesCT::class, 'import'])->name('admin_import_pricing_rules');

    Route::get('/admin/laminations', [LaminationCT::class, 'index'])->name('admin_laminations');
    Route::post('/admin/laminations/create', [LaminationCT::class, 'store'])->name('admin_store_lamination');
    Route::get('/admin/laminations/data', [LaminationCT::class, 'data'])->name('admin_data_laminations');
    Route::put('/admin/laminations/{id}/update', [LaminationCT::class, 'update'])->name('admin_update_lamination');
    Route::get('/admin/laminations/{id}/edit', [LaminationCT::class, 'edit'])->name('admin_edit_lamination');
    Route::get('/admin/laminations/{id}/details', [LaminationCT::class, 'details'])->name('admin_lamination_details');
    Route::delete('/admin/laminations/{id}/delete', [LaminationCT::class, 'destroy'])->name('admin_destroy_lamination');
    Route::get('/admin/laminations/export', [LaminationCT::class, 'export'])->name('admin_export_laminations');
    Route::post('/admin/laminations/import', [LaminationCT::class, 'import'])->name('admin_import_laminations');

    Route::get('/admin/cuttings', [CuttingCT::class, 'index'])->name('admin_cuttings');
    Route::get('/admin/cuttings/data', [CuttingCT::class, 'data'])->name('admin_data_cuttings');
    Route::get('/admin/cuttings/export', [CuttingCT::class, 'export'])->name('admin_export_cuttings');
    Route::post('/admin/cuttings/import', [CuttingCT::class, 'import'])->name('admin_import_cuttings');
    Route::post('/admin/cuttings/create', [CuttingCT::class, 'store'])->name('admin_store_cutting');
    Route::get('/admin/cuttings/{id}/edit', [CuttingCT::class, 'edit'])->name('admin_edit_cutting');
    Route::put('/admin/cuttings/{id}/update', [CuttingCT::class, 'update'])->name('admin_update_cutting');
    Route::get('/admin/cuttings/{id}/details', [CuttingCT::class, 'details'])->name('admin_cutting_details');
    Route::delete('/admin/cuttings/{id}/delete', [CuttingCT::class, 'destroy'])->name('admin_destroy_cutting');

    Route::get('/admin/lamination-pricing-rules', [LaminationPricingRuleCT::class, 'index'])->name('admin_lamination_pricing_rules');
    Route::get('/admin/lamination-pricing-rules/data', [LaminationPricingRuleCT::class, 'data'])->name('admin_data_lamination_pricing_rule');
    Route::post('/admin/lamination-pricing-rules/create', [LaminationPricingRuleCT::class, 'store'])->name('admin_store_lamination_pricing_rule');
    Route::post('/admin/lamination-pricing-rules/digital/create', [LaminationPricingRuleCT::class, 'storeDigital'])->name('admin_store_digital_lamination_pricing_rule');
    Route::get('/admin/lamination-pricing-rules/digital/{id}/details', [LaminationPricingRuleCT::class, 'detailsDigital'])->name('admin_digital_lamination_pricing_rule_details');
    Route::put('/admin/lamination-pricing-rules/digital/{id}', [LaminationPricingRuleCT::class, 'updateDigital'])->name('admin_update_digital_lamination_pricing_rule');
    Route::delete('/admin/lamination-pricing-rules/digital/{id}', [LaminationPricingRuleCT::class, 'destroyDigital'])->name('admin_delete_digital_lamination_pricing_rule');
    Route::get('/admin/lamination-pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/edit', [LaminationPricingRuleCT::class, 'edit'])->name('admin_edit_lamination_pricing_rule');
    Route::put('/admin/lamination-pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/update', [LaminationPricingRuleCT::class, 'update'])->name('admin_update_lamination_pricing_rule');
    Route::get('/admin/lamination-pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/details', [LaminationPricingRuleCT::class, 'details'])->name('admin_lamination_pricing_rule_details');
    Route::delete('/admin/lamination-pricing-rules/{engine_id}/{location_id}/{vendor_id}/{category_id}/delete', [LaminationPricingRuleCT::class, 'destroy'])->name('admin_delete_lamination_pricing_rule');
    Route::get('/admin/lamination-pricing-rules/export', [LaminationPricingRuleCT::class, 'export'])->name('admin_lamination_export_pricing_rules');
    Route::post('/admin/lamination-pricing-rules/import', [LaminationPricingRuleCT::class, 'import'])->name('admin_import_lamination_pricing_rules');

    Route::get('/admin/production-cost-finishing-laminations', [ProductionCostFinishingLaminationCT::class, 'index'])->name('admin_pc_finishing_laminations');
    Route::get('/admin/production-cost-finishing-laminations/create', [ProductionCostFinishingLaminationCT::class, 'create'])->name('admin_create_pc_finishing_lamination');
    Route::post('/admin/production-cost-finishing-laminations/create', [ProductionCostFinishingLaminationCT::class, 'store'])->name('admin_store_lamination_pc_finishing');
    Route::get('/admin/production-cost-finishing-laminations/data', [ProductionCostFinishingLaminationCT::class, 'data'])->name('admin_data_pc_finishing_lamination');
    Route::get('/admin/production-cost-finishing-laminations/digital/{id}/edit', [ProductionCostFinishingLaminationCT::class, 'editDigital'])->name('admin_edit_pc_finishing_lamination_digital');
    Route::get('/admin/production-cost-finishing-laminations/digital/{id}/details', [ProductionCostFinishingLaminationCT::class, 'detailsDigital'])->name('admin_details_pc_finishing_lamination_digital');
    Route::delete('/admin/production-cost-finishing-laminations/digital/{id}/delete', [ProductionCostFinishingLaminationCT::class, 'destroyDigital'])->name('admin_delete_pc_finishing_lamination_digital');
    Route::put('/admin/production-cost-finishing-laminations/digital/{id}/update', [ProductionCostFinishingLaminationCT::class, 'updateDigital'])->name('admin_update_pc_finishing_lamination_digital');
    Route::delete('/admin/production-cost-finishing-laminations/{id}/delete', [ProductionCostFinishingLaminationCT::class, 'destroy'])->name('admin_delete_pc_finishing_lamination');
    Route::get('/admin/production-cost-finishing-laminations/{id}/edit', [ProductionCostFinishingLaminationCT::class, 'edit'])->name('admin_edit_pc_finishing_lamination');
    Route::put('/admin/production-cost-finishing-laminations/{id}/update', [ProductionCostFinishingLaminationCT::class, 'update'])->name('admin_update_pc_finishing_lamination');
    Route::get('/admin/production-cost-finishing-laminations/{id}/details', [ProductionCostFinishingLaminationCT::class, 'details'])->name('admin_details_pc_finishing_lamination');
    Route::get('/admin/production-cost-finishing-laminations/export', [ProductionCostFinishingLaminationCT::class, 'export'])->name('admin_export_pc_finishing_laminations');
    Route::post('/admin/production-cost-finishing-laminations/import', [ProductionCostFinishingLaminationCT::class, 'import'])->name('admin_import_pc_finishing_laminations');

    Route::get('/admin/displays', [DisplayCT::class, 'index'])->name('admin_display');
    Route::get('/admin/displays/data', [DisplayCT::class, 'data'])->name('admin_display_data');
    Route::get('/admin/displays/create', [DisplayCT::class, 'create'])->name('admin_create_display');
    Route::post('/admin/displays/create', [DisplayCT::class, 'store'])->name('admin_store_display');
    Route::delete('/admin/displays/{id}/delete', [DisplayCT::class, 'destroy'])->name('admin_delete_display');
    Route::get('/admin/displays/{id}/details', [DisplayCT::class, 'details'])->name('admin_display_details');
    Route::get('/admin/displays/images/{id}', [DisplayCT::class, 'image'])->name('admin_display_image');
    Route::delete('/admin/displays/temporary/clean', [DisplayCT::class, 'cleanTemporary'])->name('admin_display_clean_temporary');
    Route::get('/admin/displays/{id}/edit', [DisplayCT::class, 'edit'])->name('admin_edit_display');
    Route::put('/admin/displays/{id}/update', [DisplayCT::class, 'update'])->name('admin_update_display');

    Route::get('/admin/display-pricing-rules', [DisplayPricingRuleCT::class, 'index'])->name('admin_display_pricing_rules');
    Route::get('/admin/display-pricing-rules/data', [DisplayPricingRuleCT::class, 'data'])->name('admin_data_display_pricing_rule');
    Route::post('/admin/display-pricing-rules/create', [DisplayPricingRuleCT::class, 'store'])->name('admin_store_display_pricing_rule');
    Route::delete('/admin/display-pricing-rules/{engine_id}/{location_id}/{category_id}/delete', [DisplayPricingRuleCT::class, 'destroy'])->name('admin_delete_display_pricing_rule');
    Route::get('/admin/display-pricing-rules/{engine_id}/{location_id}/{category_id}/details', [DisplayPricingRuleCT::class, 'details'])->name('admin_display_pricing_rule_details');
    Route::get('/admin/display-pricing-rules/{engine_id}/{location_id}/{category_id}/edit', [DisplayPricingRuleCT::class, 'edit'])->name('admin_edit_display_pricing_rule');
    Route::put('/admin/display-pricing-rules/{engine_id}/{location_id}/{category_id}/update', [DisplayPricingRuleCT::class, 'update'])->name('admin_update_display_pricing_rule');
    Route::get('/admin/display-pricing-rules/export', [DisplayPricingRuleCT::class, 'export'])->name('admin_export_display_pricing_rules');
    Route::post('/admin/display-pricing-rules/import', [DisplayPricingRuleCT::class, 'import'])->name('admin_import_display_pricing_rule');


    Route::get('/admin/production-cost-finishing-displays', [ProductionCostFinishingDisplayCT::class, 'index'])->name('admin_pc_finishing_displays');
    Route::get('/admin/production-cost-finishing-displays/create', [ProductionCostFinishingDisplayCT::class, 'create'])->name('admin_create_pc_finishing_display');
    Route::post('/admin/production-cost-finishing-displays/create', [ProductionCostFinishingDisplayCT::class, 'store'])->name('admin_store_pc_finishing_display');
    Route::get('/admin/production-cost-finishing-display/components', [ProductionCostFinishingDisplayCT::class, 'components'])->name('admin_pc_finishing_display_components');
    Route::get('/admin/production-cost-finishing-displays/data', [ProductionCostFinishingDisplayCT::class, 'data'])->name('admin_data_pc_finishing_display');
    Route::delete('/admin/production-cost-finishing-displays/{id}/delete', [ProductionCostFinishingDisplayCT::class, 'destroy'])->name('admin_destroy_pc_finishing_display');
    Route::get('/admin/production-cost-finishing-displays/{id}/add-component', [ProductionCostFinishingDisplayCT::class, 'addComponent'])->name('admin_add_component_pc_finishing_display');
    Route::post('/admin/production-cost-finishing-displays/{id}/add-component', [ProductionCostFinishingDisplayCT::class, 'storeComponent'])->name('admin_store_component_pc_finishing_display');
    Route::get('/admin/production-cost-finishing-displays/{id}/details', [ProductionCostFinishingDisplayCT::class, 'details'])->name('admin_details_pc_finishing_display');
    Route::get('/admin/production-cost-finishing-displays/{id}/edit-component', [ProductionCostFinishingDisplayCT::class, 'editComponent'])->name('admin_edit_component_pc_finishing_display');
    Route::put('/admin/production-cost-finishing-displays/{id}/update-component', [ProductionCostFinishingDisplayCT::class, 'updateComponent'])->name('admin_update_component_pc_finishing_display');

    Route::get('/admin/gallery-samples', [GallerySampleCT::class, 'index'])->name('admin_gallery_samples');
    Route::get('/admin/gallery-samples/create', [GallerySampleCT::class, 'create'])->name('admin_create_gallery_samples');
    Route::post('/admin/gallery-samples/create', [GallerySampleCT::class, 'store'])->name('admin_store_sample_gallery');
    Route::get('/admin/gallery-samples/data', [GallerySampleCT::class, 'data'])->name('admin_gallery_samples_data');
    Route::get('/admin/gallery-samples/{engine_id}/{category_id}/edit', [GallerySampleCT::class, 'edit'])->name('admin_gallery_samples_edit');
    Route::delete('/admin/gallery-samples', [GallerySampleCT::class, 'destroy'])->name('admin_gallery_samples_destroy');
    Route::get('/admin/gallery-samples/images/{id}', [GallerySampleCT::class, 'image'])->name('admin_gallery_sample_image');
    Route::delete('/admin/gallery-samples/temporary/clean', [GallerySampleCT::class, 'cleanTemporary'])->name('admin_gallery_sample_clean_temporary');
    Route::put('/admin/gallery-samples/{engine_id}/{category_id}', [GallerySampleCT::class, 'update'])->name('admin_gallery_samples_update');
    Route::get('/admin/gallery-samples/{engine_id}/{category_id}/details', [GallerySampleCT::class, 'details'])->name('admin_gallery_samples_details');

    Route::get('/admin/discounts', [DiscountCT::class, 'index'])->name('admin_discounts');
    Route::get('/admin/discounts/data', [DiscountCT::class, 'data'])->name('admin_data_discounts');
    Route::post('/admin/discounts/create', [DiscountCT::class, 'store'])->name('admin_store_discount');
    Route::get('/admin/discounts/{id}/edit', [DiscountCT::class, 'edit'])->name('admin_edit_discount');
    Route::get('/admin/discounts/{id}/details', [DiscountCT::class, 'details'])->name('admin_discount_details');
    Route::put('/admin/discounts/{id}/update', [DiscountCT::class, 'update'])->name('admin_update_discount');
    Route::delete('/admin/discounts/{id}/delete', [DiscountCT::class, 'destroy'])->name('admin_destroy_discount');

    Route::get('/admin/production-cost-finishing-cuttings', [ProductionCostFinishingCuttingCT::class, 'index'])->name('admin_production_cost_finishing_cuttings');
    Route::get('/admin/production-cost-finishing-cuttings/data', [ProductionCostFinishingCuttingCT::class, 'data'])->name('admin_data_pc_finishing_cuttings');
    Route::get('/admin/production-cost-finishing-cuttings/create', [ProductionCostFinishingCuttingCT::class, 'create'])->name('admin_create_pc_finishing_cutting');
    Route::post('/admin/production-cost-finishing-cuttings/create', [ProductionCostFinishingCuttingCT::class, 'store'])->name('admin_store_pc_finishing_cutting');
    Route::get('/admin/production-cost-finishing-cuttings/{id}/edit', [ProductionCostFinishingCuttingCT::class, 'edit'])->name('admin_edit_pc_finishing_cutting');
    Route::put('/admin/production-cost-finishing-cuttings/{id}/update', [ProductionCostFinishingCuttingCT::class, 'update'])->name('admin_update_pc_finishing_cutting');
    Route::get('/admin/production-cost-finishing-cuttings/{id}/details', [ProductionCostFinishingCuttingCT::class, 'details'])->name('admin_details_pc_finishing_cutting');
    Route::delete('/admin/production-cost-finishing-cuttings/{id}/delete', [ProductionCostFinishingCuttingCT::class, 'destroy'])->name('admin_destroy_pc_finishing_cutting');

    Route::get('/admin/production-cost-finishing-potong/create', [ProductionCostFinishingCuttingCT::class, 'createPotong'])->name('admin_create_pc_finishing_potong');
    Route::post('/admin/production-cost-finishing-potong/create', [ProductionCostFinishingCuttingCT::class, 'storePotong'])->name('admin_store_pc_finishing_potong');
    Route::get('/admin/production-cost-finishing-potong/{id}/edit', [ProductionCostFinishingCuttingCT::class, 'editPotong'])->name('admin_edit_pc_finishing_potong');
    Route::put('/admin/production-cost-finishing-potong/{id}/update', [ProductionCostFinishingCuttingCT::class, 'updatePotong'])->name('admin_update_pc_finishing_potong');
    Route::get('/admin/production-cost-finishing-potong/{id}/details', [ProductionCostFinishingCuttingCT::class, 'detailsPotong'])->name('admin_details_pc_finishing_potong');
    Route::delete('/admin/production-cost-finishing-potong/{id}/delete', [ProductionCostFinishingCuttingCT::class, 'destroyPotong'])->name('admin_destroy_pc_finishing_potong');

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA - OFFSET
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/offset-master', [OffsetMasterCT::class, 'index'])->name('admin_offset_master');
    Route::get('/admin/offset-master/data-plano', [OffsetMasterCT::class, 'dataPlano'])->name('admin_data_offset_plano');
    Route::post('/admin/offset-master/store-plano', [OffsetMasterCT::class, 'storePlano'])->name('admin_store_offset_plano');
    Route::get('/admin/offset-master/data-plates', [OffsetMasterCT::class, 'dataPlates'])->name('admin_data_offset_plates');
    Route::post('/admin/offset-master/store-plate', [OffsetMasterCT::class, 'storePlate'])->name('admin_store_offset_plate');
    Route::get('/admin/offset-master/data-print-costs', [OffsetMasterCT::class, 'dataPrintCosts'])->name('admin_data_offset_print_costs');
    Route::post('/admin/offset-master/store-print-cost', [OffsetMasterCT::class, 'storePrintCost'])->name('admin_store_offset_print_cost');
    Route::get('/admin/offset-master/data-pricing-rules', [OffsetMasterCT::class, 'dataPricingRules'])->name('admin_data_offset_pricing_rules');
    Route::post('/admin/offset-master/store-pricing-rule', [OffsetMasterCT::class, 'storePricingRule'])->name('admin_store_offset_pricing_rule');
    Route::get('/admin/offset-master/data-products', [OffsetMasterCT::class, 'dataProducts'])->name('admin_data_offset_products');
    Route::post('/admin/offset-master/store-product', [OffsetMasterCT::class, 'storeProduct'])->name('admin_store_offset_product');
    Route::delete('/admin/offset-master/delete-plano/{id}', [OffsetMasterCT::class, 'destroyPlano'])->name('admin_delete_offset_plano');
    Route::delete('/admin/offset-master/delete-plate/{id}', [OffsetMasterCT::class, 'destroyPlate'])->name('admin_delete_offset_plate');
    Route::delete('/admin/offset-master/delete-print-cost/{id}', [OffsetMasterCT::class, 'destroyPrintCost'])->name('admin_delete_offset_print_cost');
    Route::delete('/admin/offset-master/delete-pricing-rule/{id}', [OffsetMasterCT::class, 'destroyPricingRule'])->name('admin_delete_offset_pricing_rule');
    Route::delete('/admin/offset-master/delete-product/{id}', [OffsetMasterCT::class, 'destroyProduct'])->name('admin_delete_offset_product');
    Route::put('/admin/offset-master/update-plano/{id}', [OffsetMasterCT::class, 'updatePlano'])->name('admin_update_offset_plano');
    Route::put('/admin/offset-master/update-plate/{id}', [OffsetMasterCT::class, 'updatePlate'])->name('admin_update_offset_plate');
    Route::put('/admin/offset-master/update-print-cost/{id}', [OffsetMasterCT::class, 'updatePrintCost'])->name('admin_update_offset_print_cost');
    Route::put('/admin/offset-master/update-pricing-rule/{id}', [OffsetMasterCT::class, 'updatePricingRule'])->name('admin_update_offset_pricing_rule');
    Route::put('/admin/offset-master/update-product/{id}', [OffsetMasterCT::class, 'updateProduct'])->name('admin_update_offset_product');
});
