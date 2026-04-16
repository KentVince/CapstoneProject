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
use App\Http\Middleware\RedirectPanelUserToWelcome;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->favicon(asset('images/CofSys_LOGO.png'))
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
            ->databaseNotificationsPolling('8s')
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
                RedirectPanelUserToWelcome::class,
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

            // "New" badge overlay on the checkbox cell for unread pending records
            // Visual: pure CSS ::after driven by server-side recordClasses — never stripped by Livewire morphdom
            // Click: event delegation on document — never needs re-attachment
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
                    <style>
                        tr.new-unread-record td:first-child {
                            position: relative;
                            cursor: pointer;
                        }
                        tr.new-unread-record td:first-child::after {
                            content: 'New';
                            position: absolute;
                            top: 4px;
                            right: 2px;
                            background: #ef4444;
                            color: #fff;
                            font-size: 9px;
                            font-weight: 700;
                            padding: 1px 6px;
                            border-radius: 9999px;
                            line-height: 1.5;
                            z-index: 10;
                            letter-spacing: 0.04em;
                            pointer-events: none;
                        }
                        tr.new-unread-record td:first-child:hover::after {
                            background: #dc2626;
                        }
                    </style>
                    <script>
                        (function() {
                            // Single delegated listener on document — survives all Livewire DOM morphs
                            document.addEventListener('click', function(e) {
                                var td = e.target.closest('tr.new-unread-record td:first-child');
                                if (!td) return;

                                e.stopPropagation();
                                e.preventDefault();

                                var row = td.closest('tr');
                                var wireKey = row ? (row.getAttribute('wire:key') || '') : '';
                                var recordId = wireKey.split('.').pop();
                                if (!recordId) return;

                                var components = window.Livewire ? window.Livewire.all() : [];
                                for (var i = 0; i < components.length; i++) {
                                    try {
                                        var wire = components[i].$wire;
                                        if (wire && typeof wire.mountTableAction === 'function') {
                                            wire.mountTableAction('view', recordId);
                                            return;
                                        }
                                    } catch(err) {}
                                }
                            });
                        })();
                    </script>
                    HTML
            );

            // Register the change password modal
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@livewire(\'change-password-modal\')')
            );

            // Register the pest disease approval modal
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@livewire(\'pest-disease-approval-modal\')')
            );


            // Unified real-time polling: sidebar badges + notification bell — all in sync
            FilamentView::registerRenderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
                    <script>
                        (function() {
                            const POLL_INTERVAL = 8000; // 8s — matches databaseNotificationsPolling
                            let lastDetectionCount = null;
                            let lastSoilCount = null;

                            // ── Helpers ────────────────────────────────────────────────

                            function findNavItem(label) {
                                const items = document.querySelectorAll('.fi-sidebar-item');
                                for (const item of items) {
                                    const el = item.querySelector('.fi-sidebar-item-label');
                                    if (el && el.textContent.trim() === label) return item;
                                }
                                return null;
                            }

                            function setBadge(item, count) {
                                if (!item) return;
                                let badge = item.querySelector('.fi-badge');
                                if (count > 0) {
                                    if (badge) {
                                        const textEl = badge.querySelector('.truncate') || badge.querySelector('span');
                                        if (textEl) textEl.textContent = count;
                                        badge.style.display = '';
                                    } else {
                                        const btn = item.querySelector('.fi-sidebar-item-button');
                                        if (btn) {
                                            const span = document.createElement('span');
                                            span.className = 'fi-badge fi-color-warning fi-size-sm rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-warning-500/10 text-warning-700 ring-warning-600/20 dark:bg-warning-500/20 dark:text-warning-400 dark:ring-warning-500/30';
                                            span.innerHTML = '<span class="grid"><span class="truncate">' + count + '</span></span>';
                                            btn.appendChild(span);
                                        }
                                    }
                                } else {
                                    if (badge) badge.style.display = 'none';
                                }
                            }

                            // Force Filament's database-notifications Livewire component to refresh immediately
                            function refreshNotificationBell() {
                                if (!window.Livewire) return;
                                Livewire.all().forEach(function(component) {
                                    try {
                                        const el = component.el;
                                        if (el && (
                                            el.querySelector('.fi-notifications') ||
                                            el.querySelector('[x-ref="notificationsContainer"]') ||
                                            (el.hasAttribute('wire:poll') && el.id && el.id.includes('notification'))
                                        )) {
                                            component.$wire.$refresh();
                                        }
                                    } catch(e) {}
                                });
                                // Also dispatch Filament's internal event as fallback
                                document.dispatchEvent(new CustomEvent('filament:notification-received'));
                            }

                            // ── Unified poll ───────────────────────────────────────────

                            function pollAll() {
                                // Detection badge
                                fetch('/admin/api/pending-detections-count', {
                                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    var count = data.count;
                                    if (lastDetectionCount !== null && count !== lastDetectionCount) {
                                        // New detection arrived — force notification bell to refresh NOW
                                        refreshNotificationBell();
                                    }
                                    if (lastDetectionCount !== count) {
                                        lastDetectionCount = count;
                                        setBadge(findNavItem('Detections'), count);
                                    }
                                })
                                .catch(function() {});

                                // Soil badge
                                fetch('/admin/api/pending-soil-count', {
                                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    var count = data.count;
                                    if (lastSoilCount !== null && count !== lastSoilCount) {
                                        refreshNotificationBell();
                                    }
                                    if (lastSoilCount !== count) {
                                        lastSoilCount = count;
                                        setBadge(findNavItem('Soil Analysis'), count);
                                    }
                                })
                                .catch(function() {});
                            }

                            document.addEventListener('DOMContentLoaded', pollAll);
                            setInterval(pollAll, POLL_INTERVAL);

                            document.addEventListener('livewire:navigated', function() {
                                lastDetectionCount = null;
                                lastSoilCount = null;
                                pollAll();
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
