<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use App\Filament\Pages\Auth\Login;
use Filament\Support\Colors\Color;
use App\Filament\Pages\Auth\Register;
use Filament\Navigation\MenuItem;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use App\Rules\CurrentPassword;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->favicon(asset('images/CAFARM_LOGO.png'))
            ->brandLogo(asset('images/favicon.png'))
            ->brandLogoHeight('80px')
            ->sidebarCollapsibleOnDesktop()
            ->login(Login::class)
            ->registration(Register::class)
            ->colors([
                'primary' => Color::Green,
            ])
            ->userMenuItems([
                'change-password' => MenuItem::make()
                    ->label('Change Password')
                    ->icon('heroicon-o-key')
                    ->url('javascript:void(0)')
                    ->openUrlInNewTab(false),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     Widgets\FilamentInfoWidget::class,
            // ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])



            ->authMiddleware([
                Authenticate::class,
            ])

            ->plugins([

                FilamentApexChartsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),


            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
        }

        public function boot(): void
        {
            Filament::serving(function () {
                Filament::registerNavigationGroups([
                    'Pest and Disease',   // First group
                    'Soil Fertility',     // Second group
                    'Maps',               // Third group
                    'Information Center', // Fourth group
                    'User Management',    // Fifth group (last)
                ]);
            });

            // Register the change password modal
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@livewire(\'change-password-modal\')')
            );

            // Poll pending detections count and update sidebar badge in real-time
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
                    <script>
                        (function() {
                            let lastCount = null;

                            function findRecordsBadge() {
                                const items = document.querySelectorAll('.fi-sidebar-item');
                                for (const item of items) {
                                    const label = item.querySelector('.fi-sidebar-item-label');
                                    if (label && label.textContent.trim() === 'Records') {
                                        return {
                                            item: item,
                                            badgeWrapper: item.querySelector('.fi-sidebar-item-button > span:last-child:not(.fi-sidebar-item-label):not(.fi-sidebar-item-icon)'),
                                            badge: item.querySelector('.fi-badge')
                                        };
                                    }
                                }
                                return null;
                            }

                            function updateBadge() {
                                fetch('/admin/api/pending-detections-count', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    var count = data.count;
                                    if (lastCount === count) return;
                                    lastCount = count;

                                    var result = findRecordsBadge();
                                    if (!result) return;

                                    if (count > 0) {
                                        if (result.badge) {
                                            // Badge exists in DOM, update its text
                                            var textEl = result.badge.querySelector('.truncate') || result.badge.querySelector('span span');
                                            if (textEl) textEl.textContent = count;
                                            // Make sure it's visible
                                            if (result.badge.parentElement) {
                                                result.badge.parentElement.style.display = '';
                                            }
                                        } else {
                                            // Badge doesn't exist yet, do a soft SPA refresh to render it
                                            if (window.Livewire && window.Livewire.navigate) {
                                                window.Livewire.navigate(window.location.href);
                                            }
                                        }
                                    } else {
                                        // Count is 0, hide the badge
                                        if (result.badge && result.badge.parentElement) {
                                            result.badge.parentElement.style.display = 'none';
                                        }
                                    }
                                })
                                .catch(function() {});
                            }

                            // Poll every 10 seconds
                            setInterval(updateBadge, 10000);

                            // Re-check after Livewire SPA navigations
                            document.addEventListener('livewire:navigated', function() {
                                lastCount = null;
                                setTimeout(updateBadge, 1000);
                            });

                            // Initial check after page load
                            document.addEventListener('DOMContentLoaded', function() {
                                setTimeout(updateBadge, 2000);
                            });
                        })();
                    </script>
                    HTML
            );

            // Add JavaScript to handle modal opening
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
                    <script>
                        document.addEventListener('livewire:navigated', function() {
                            console.log('Livewire navigated - attaching handler');
                            attachChangePasswordHandler();
                        });

                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('DOM loaded - attaching handler');
                            attachChangePasswordHandler();
                            checkLivewireComponent();
                        });

                        function checkLivewireComponent() {
                            setTimeout(function() {
                                const livewireComponents = document.querySelectorAll('[wire\\:id]');
                                console.log('Found Livewire components:', livewireComponents.length);

                                livewireComponents.forEach(function(comp) {
                                    console.log('Livewire component:', comp.getAttribute('wire:id'));
                                });

                                const modal = document.querySelector('#change-password-modal');
                                console.log('Modal element found:', modal ? 'YES' : 'NO');
                            }, 1000);
                        }

                        function attachChangePasswordHandler() {
                            setTimeout(function() {
                                // Find all menu items and look for the one with "Change Password" text
                                const menuItems = document.querySelectorAll('[role="menuitem"], .fi-dropdown-list-item');
                                console.log('Found menu items:', menuItems.length);

                                menuItems.forEach(function(item) {
                                    const text = item.textContent.trim();

                                    if (text.includes('Change Password')) {
                                        console.log('Found Change Password menu item!');

                                        // Remove existing listener to avoid duplicates
                                        const newItem = item.cloneNode(true);
                                        item.parentNode.replaceChild(newItem, item);

                                        newItem.addEventListener('click', function(e) {
                                            console.log('Change Password clicked!');
                                            e.preventDefault();
                                            e.stopPropagation();

                                            // Dispatch Livewire event
                                            console.log('Dispatching openChangePasswordModal event');
                                            window.Livewire.dispatch('openChangePasswordModal');

                                            // Also try to set the isOpen property directly on the component
                                            setTimeout(function() {
                                                const allComponents = window.Livewire.all();
                                                console.log('Setting isOpen directly on components...');

                                                allComponents.forEach(function(component, index) {
                                                    try {
                                                        // Livewire 3 uses $wire for properties
                                                        const wire = component.$wire || component;
                                                        console.log('Component ' + index + ' checking for isOpen...');

                                                        // Try to access isOpen directly
                                                        try {
                                                            const currentIsOpen = wire.isOpen;
                                                            console.log('Component ' + index + ' isOpen value:', currentIsOpen);

                                                            // If isOpen exists (even if undefined), this is our component!
                                                            if (typeof currentIsOpen !== 'undefined' || 'isOpen' in wire) {
                                                                console.log('Found change password component! Setting isOpen to true');

                                                                // Set isOpen using Livewire 3 API
                                                                wire.isOpen = true;

                                                                // Find and update Alpine element directly
                                                                const alpineEl = component.el.querySelector('[x-data]');
                                                                console.log('Alpine element found:', !!alpineEl);

                                                                if (alpineEl && alpineEl._x_dataStack && alpineEl._x_dataStack.length > 0) {
                                                                    console.log('Setting Alpine isOpen to true');
                                                                    alpineEl._x_dataStack[0].isOpen = true;
                                                                }
                                                            }
                                                        } catch (accessErr) {
                                                            console.log('Component ' + index + ' no isOpen property');
                                                        }
                                                    } catch (err) {
                                                        console.log('Error with component ' + index + ':', err.message);
                                                    }
                                                });
                                            }, 100);
                                        });
                                    }
                                });
                            }, 500);
                        }
                    </script>
                    HTML
            );
        }

    }
