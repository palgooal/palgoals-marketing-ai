<?php

use App\Http\Controllers\Brand\BrandProfileController;
use App\Http\Controllers\Analysis\PageAnalysisController;
use App\Http\Controllers\AI\AiTestController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Content\ContentGenerationController;
use App\Http\Controllers\Knowledge\KnowledgeDocumentController;
use App\Http\Controllers\Offers\OfferGenerationController;
use App\Http\Controllers\Plans\StrategyPlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Prompts\PromptTemplateController;
use App\Http\Controllers\Services\BrandServiceController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Templates\TemplateCategoryController;
use App\Http\Controllers\Templates\TemplateController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('brand-profile')->name('brand.')->group(function () {
        Route::get('/', [BrandProfileController::class, 'edit'])->name('edit');
        Route::put('/', [BrandProfileController::class, 'update'])->name('update');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'edit'])->name('edit');
        Route::put('/', [SettingController::class, 'update'])->name('update');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [BrandServiceController::class, 'index'])->name('index');
        Route::get('/create', [BrandServiceController::class, 'create'])->name('create');
        Route::post('/', [BrandServiceController::class, 'store'])->name('store');
        Route::get('/{brandService}/edit', [BrandServiceController::class, 'edit'])->name('edit');
        Route::put('/{brandService}', [BrandServiceController::class, 'update'])->name('update');
    });

    Route::prefix('template-categories')->name('template-categories.')->group(function () {
        Route::get('/', [TemplateCategoryController::class, 'index'])->name('index');
        Route::get('/create', [TemplateCategoryController::class, 'create'])->name('create');
        Route::post('/', [TemplateCategoryController::class, 'store'])->name('store');
        Route::get('/{templateCategory}/edit', [TemplateCategoryController::class, 'edit'])->name('edit');
        Route::put('/{templateCategory}', [TemplateCategoryController::class, 'update'])->name('update');
    });

    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/', [TemplateController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [TemplateController::class, 'update'])->name('update');
    });

    Route::prefix('knowledge-documents')->name('knowledge-documents.')->group(function () {
        Route::get('/', [KnowledgeDocumentController::class, 'index'])->name('index');
        Route::get('/create', [KnowledgeDocumentController::class, 'create'])->name('create');
        Route::post('/', [KnowledgeDocumentController::class, 'store'])->name('store');
        Route::get('/{knowledgeDocument}/edit', [KnowledgeDocumentController::class, 'edit'])->name('edit');
        Route::put('/{knowledgeDocument}', [KnowledgeDocumentController::class, 'update'])->name('update');
    });

    Route::prefix('prompt-templates')->name('prompts.')->group(function () {
        Route::get('/', [PromptTemplateController::class, 'index'])->name('index');
        Route::get('/create', [PromptTemplateController::class, 'create'])->name('create');
        Route::post('/', [PromptTemplateController::class, 'store'])->name('store');
        Route::get('/{promptTemplate}/edit', [PromptTemplateController::class, 'edit'])->name('edit');
        Route::put('/{promptTemplate}', [PromptTemplateController::class, 'update'])->name('update');
    });

    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [ContentGenerationController::class, 'index'])->name('index');
        Route::get('/create', [ContentGenerationController::class, 'create'])->name('create');
        Route::post('/generate', [ContentGenerationController::class, 'store'])->name('generate');
        Route::get('/{contentGeneration}', [ContentGenerationController::class, 'show'])->name('show');
    });

    Route::prefix('offers')->name('offers.')->group(function () {
        Route::get('/', [OfferGenerationController::class, 'index'])->name('index');
        Route::get('/create', [OfferGenerationController::class, 'create'])->name('create');
        Route::post('/generate', [OfferGenerationController::class, 'store'])->name('generate');
        Route::get('/{offerGeneration}', [OfferGenerationController::class, 'show'])->name('show');
    });

    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [StrategyPlanController::class, 'index'])->name('index');
        Route::get('/create', [StrategyPlanController::class, 'create'])->name('create');
        Route::post('/generate', [StrategyPlanController::class, 'store'])->name('generate');
        Route::get('/{strategyPlan}', [StrategyPlanController::class, 'show'])->name('show');
    });

    Route::prefix('analysis')->name('analysis.')->group(function () {
        Route::get('/', [PageAnalysisController::class, 'index'])->name('index');
        Route::get('/create', [PageAnalysisController::class, 'create'])->name('create');
        Route::post('/run', [PageAnalysisController::class, 'store'])->name('run');
        Route::get('/{pageAnalysis}', [PageAnalysisController::class, 'show'])->name('show');
    });

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/test', [AiTestController::class, 'show'])->name('test.show');
        Route::post('/test', [AiTestController::class, 'store'])->name('test.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
