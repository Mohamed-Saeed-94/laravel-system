<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use Coolsam\Modules\ModulesPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(ModulesPlugin::make())
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverResources(
                in: base_path('Modules/Core/app/Filament/Resources'),
                for: 'Modules\\Core\\Filament\\Resources'
            )
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->userMenuItems([
                'switch_to_ar' => Action::make('switch_to_ar')
                    ->label(__('core::actions.switch_to_ar'))
                    ->icon('heroicon-o-language')
                    ->url(fn () => route('locale.switch', ['locale' => 'ar']))
                    ->visible(fn (): bool => app()->getLocale() !== 'ar'),

                'switch_to_en' => Action::make('switch_to_en')
                    ->label(__('core::actions.switch_to_en'))
                    ->icon('heroicon-o-language')
                    ->url(fn () => route('locale.switch', ['locale' => 'en']))
                    ->visible(fn (): bool => app()->getLocale() !== 'en'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                SetLocale::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
