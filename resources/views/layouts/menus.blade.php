<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="index" class="sidebar-logo">
            <img src="{{ URL::asset('') }}assets/images/logo.png" alt="site logo" class="light-logo">
            <img src="{{ URL::asset('') }}assets/images/logo-light.png" alt="site logo" class="dark-logo">
            <img src="{{ URL::asset('') }}assets/images/logo-icon.png" alt="site logo" class="logo-icon">
        </a>
    </div>

    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                <li
                    class="dropdown {{ Route::is('index') ? 'open' : '' }}{{ Route::is('pharma-index') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="solar:home-2-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Tableau de bord</span>
                    </a>
                    @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                        <ul class="sidebar-submenu {{ Route::is('index') ? 'show' : '' }}">
                            <li class="{{ Route::is('index') ? 'active-page' : '' }}">
                                <a href="{{ url('index') }}" class="{{ Route::is('index') ? 'active-page' : '' }}">
                                    <i class="ri-circle-fill circle-icon text-primary-600"></i>
                                    Tableau de bord</a>
                            </li>
                        </ul>
                    @endif
                </li>
            @endif
            <li class="sidebar-menu-group-title">Menus</li>
            @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                <li class="dropdown {{ Route::is('users', 'user-add', 'view-profile') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Utilisateurs</span>
                    </a>
                    <ul
                        class="sidebar-submenu {{ Route::is('users', 'user-add', 'view-profile') ? 'show' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'show' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'show' : '' }}">
                        <li
                            class="{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}">
                            <a href="{{ url('users') }}"
                                class="{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Tous les utilisateurs</a>
                        </li>
                    </ul>
                </li>
            @endif
            <li
                class="dropdown {{ Route::is('assurance', 'pharmacy', 'garde', 'commune', 'medicament', 'requete', 'reponse', 'reservation', 'view-pharmacy', 'add-pharmacy', 'paiement') ? 'open' : '' }}">
                <a href="javascript:void(0)">
                    <iconify-icon icon="healthicons:pharmacy-24px" class="menu-icon"></iconify-icon>
                    <span>Pharmacies</span>
                </a>
                <ul
                    class="sidebar-submenu {{ Route::is('assurance', 'pharmacy', 'garde', 'commune', 'medicament', 'requete', 'reponse', 'reservation', 'view-pharmacy', 'add-pharmacy', 'paiement') ? 'show' : '' }}">
                    @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                        <li
                            class="{{ Route::is('pharmacy') ? 'active-page' : '' }}
                    {{ Route::is('view-pharmacy') ? 'active-page' : '' }}
                    {{ Route::is('add-pharmacy') ? 'active-page' : '' }}
                     ">
                            <a href="{{ url('pharmacy') }}"
                                class="{{ Route::is('view-pharmacy') ? 'active-page' : '' }}
                        {{ Route::is('pharmacy') ? 'active-page' : '' }}
                        {{ Route::is('add-pharmacy') ? 'active-page' : '' }}
                         ">
                                <i class="ri-store-2-fill text-success-main"></i>
                                Pharmacies</a>
                        </li>
                        <li class="{{ Route::is('garde') ? 'active-page' : '' }}">
                            <a href="{{ url('garde') }}" class="{{ Route::is('garde') ? 'active-page' : '' }}">
                                <i class="ri-alarm-warning-fill text-warning-main"></i>
                                Pharmacie de garde</a>
                        </li>
                        <li class="{{ Route::is('assurance') ? 'active-page' : '' }}">
                            <a href="{{ url('assurance') }}"
                                class="{{ Route::is('assurance') ? 'active-page' : '' }}">
                                <i class="ri-shield-check-fill text-info-main"></i>
                                Assurances</a>
                        </li>
                        <li class="{{ Route::is('paiement') ? 'active-page' : '' }}">
                            <a href="{{ url('paiement') }}" class="{{ Route::is('paiement') ? 'active-page' : '' }}">
                                <i class="ri-bank-card-fill text-primary"></i>
                                Moyen de paiement</a>
                        </li>
                        <li class="{{ Route::is('commune') ? 'active-page' : '' }}">
                            <a href="{{ url('commune') }}" class="{{ Route::is('commune') ? 'active-page' : '' }}">
                                <i class="ri-map-pin-2-fill text-danger-main"></i>
                                Communes / Villes</a>
                        </li>
                        <li class="{{ Route::is('medicament') ? 'active-page' : '' }}">
                            <a href="{{ url('medicament') }}"
                                class="{{ Route::is('medicament') ? 'active-page' : '' }}">
                                <i class="ri-capsule-fill text-danger-main"></i>
                                Médicaments</a>
                        </li>
                    @endif
                    @if (Auth::user()->role != 'ADMIN' || Auth::user()->role != 'SUPERADMIN')
                        @if (Auth::user()->role == 'PHARMACIEN' || Auth::user()->role == 'GESTIONNAIRE')
                            <li class="{{ Route::is('requete') ? 'active-page' : '' }}">
                                <a href="{{ url('requete') }}"
                                    class="{{ Route::is('requete') ? 'active-page' : '' }}">
                                    <i class="ri-file-list-3-fill text-primary"></i>
                                    Requêtes</a>
                            </li>
                            <li class="{{ Route::is('reponse') ? 'active-page' : '' }}">
                                <a href="{{ url('reponse') }}"
                                    class="{{ Route::is('reponse') ? 'active-page' : '' }}">
                                    <i class="ri-reply-all-fill text-info-main"></i>
                                    Reponses</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN' || Auth::user()->role == 'GESTIONNAIRE')
                            <li class="{{ Route::is('reservation') ? 'active-page' : '' }}">
                                <a href="{{ url('reservation') }}"
                                    class="{{ Route::is('reservation') ? 'active-page' : '' }}">
                                    <i class="ri-calendar-check-fill text-success-main"></i>
                                    Réservations</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN')
                            <li class="{{ Route::is('rechargement') ? 'active-page' : '' }}">
                                <a href="{{ url('rechargement') }}"
                                    class="{{ Route::is('rechargement') ? 'active-page' : '' }}">
                                    <i class="ri-wallet-3-fill text-warning-main"></i>
                                    Rechargements</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN' || Auth::user()->role == 'CAISSIERE')
                            <li class="{{ Route::is('transactions') ? 'active-page' : '' }}">
                                <a href="{{ url('transactions') }}"
                                    class="{{ Route::is('transactions') ? 'active-page' : '' }}">
                                    <i class="ri-exchange-dollar-fill text-success-main"></i>
                                    Transactions</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
            @if (Auth::user()->role == 'SUPERADMIN')
                <li class="dropdown {{ Route::is('publicites') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="ri-megaphone-fill"></i>
                        <span>Publicites</span>
                    </a>
                    <ul class="sidebar-submenu {{ Route::is('publicites') ? 'show' : '' }}">
                        <li class="{{ Route::is('publicites') ? 'active-page' : '' }}
                     ">
                            <a href="{{ url('publicites') }}"
                                class="{{ Route::is('publicites') ? 'active-page' : '' }}
                         "><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Publicites</a>
                        </li>
                    </ul>
                </li>
                @endif
                @if (Auth::user()->role == 'SUPERADMIN' || Auth::user()->role == 'ADMIN')
                <li class="dropdown {{ Route::is('categories') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        {{-- <iconify-icon icon="healthicons:vaccines-outline" class="menu-icon"></iconify-icon> --}}
                        <i class="ri-syringe-fill text-secondary"></i>
                        <span>Vaccins</span>
                    </a>
                    <ul class="sidebar-submenu {{ Route::is('categories') ? 'show' : '' }}">
                        <li class="{{ Route::is('categories') ? 'active-page' : '' }}">
                            <a href="{{ url('categories') }}"
                                class="{{ Route::is('categories') ? 'active-page' : '' }}
                         ">
                                <i class="ri-price-tag-3-fill text-primary"></i>
                                Categories</a>
                        </li>
                        <li class="{{ Route::is('vaccins') ? 'active-page' : '' }}">
                            <a href="{{ url('vaccins') }}"
                                class="{{ Route::is('vaccins') ? 'active-page' : '' }}
                         ">
                                <i class="ri-syringe-fill text-success-main"></i>
                                Vaccins</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                <li class="dropdown {{ Route::is('rechargements') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="ri-wallet-3-fill"></i>
                        <span>Rechargements</span>
                    </a>
                    <ul class="sidebar-submenu {{ Route::is('rechargements') ? 'show' : '' }}">
                        <li class="{{ Route::is('rechargements') ? 'active-page' : '' }}">
                            <a href="{{ url('rechargements') }}"
                                class="{{ Route::is('rechargements') ? 'active-page' : '' }}">
                                <i class="ri-time-fill text-warning"></i>
                                En attente
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                <li class="sidebar-menu-group-title">Paramètres</li>
                <li class="{{ Route::is('qrcode') ? 'active-page' : '' }}">
                    <a href="{{ url('qrcode') }}" class="{{ Route::is('qrcode') ? 'active-page' : '' }}">
                        <iconify-icon icon="solar:qr-code-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>QrCode Pharmacie</span>
                    </a>
                </li>
                <li class="{{ Route::is('pricing') ? 'active-page' : '' }}">
                    <a href="{{ url('pricing') }}" class="{{ Route::is('pricing') ? 'active-page' : '' }}">
                        <iconify-icon icon="solar:wallet-money-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Forfaits</span>
                    </a>
                </li>
                <li class="{{ Route::is('terms-about', 'add-about', 'edit-about') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-about') }}"
                        class="{{ Route::is('terms-about', 'add-about', 'edit-about') ? 'active-page' : '' }}">

                        <iconify-icon icon="solar:info-circle-bold" class="menu-icon"></iconify-icon>
                        <span>A propos de nous</span>
                    </a>
                </li>


                <li class="{{ Route::is('terms-politicy', 'add-politicy', 'edit-politicy') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-politicy') }}"
                        class="{{ Route::is('terms-politicy', 'add-politicy', 'edit-politicy') ? 'active-page' : '' }}">

                        <iconify-icon icon="solar:shield-keyhole-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Politique de confidentialité</span>
                    </a>
                </li>


                <li class="{{ Route::is('terms-mention', 'add-mention', 'edit-mention') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-mention') }}"
                        class="{{ Route::is('terms-mention', 'add-mention', 'edit-mention') ? 'active-page' : '' }}">

                        <iconify-icon icon="solar:document-text-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Mentions légales</span>
                    </a>
                </li>


                <li class="{{ Route::is('terms-aide', 'add-aide', 'edit-aide') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-aide') }}"
                        class="{{ Route::is('terms-aide', 'add-aide', 'edit-aide') ? 'active-page' : '' }}">

                        <iconify-icon icon="solar:question-circle-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Aide</span>
                    </a>
                </li>


                <li
                    class="{{ Route::is('terms-condition', 'add-condition', 'edit-condition') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-condition') }}"
                        class="{{ Route::is('terms-condition', 'add-condition', 'edit-condition') ? 'active-page' : '' }}">

                        <iconify-icon icon="solar:clipboard-text-bold-duotone" class="menu-icon"></iconify-icon>
                        <span>Conditions générales</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->role == 'SUPERADMIN' || Auth::user()->role == 'PHARMACIEN')
                <li
                    class="dropdown {{ Route::is('company', 'pharmacien', 'notification', 'notification-alert', 'payment-gateway') ? 'show' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                        <span>Paramètres</span>
                    </a>
                    <ul
                        class="sidebar-submenu {{ Route::is('company', 'notification', 'notification-alert', 'payment-gateway') ? 'show' : '' }}">
                        @if (Auth::user()->role == 'SUPERADMIN')
                            <li class="{{ Route::is('company') ? 'active-page' : '' }}">
                                <a href="company" class="{{ Route::is('company') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                    Admin</a>
                            </li>
                            <li class="{{ Route::is('user-pharma') ? 'active-page' : '' }}">
                                <a href="user-pharma" class="{{ Route::is('user-pharma') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                    Pharmacien</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN')
                            <li class="{{ Route::is('pharmacien') ? 'active-page' : '' }}">
                                <a href="{{ url('pharmacien') }}"
                                    class="{{ Route::is('pharmacien') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                    Utilisateurs</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- ═══════════════════════════════
                DÉCONNEXION
            ═══════════════════════════════ --}}
            <li class="mt-20">
                <a href="{{ url('logout') }}" class="bg-danger-50 text-danger radius-12">
                    <iconify-icon icon="solar:logout-2-bold-duotone" class="menu-icon"></iconify-icon>

                    <span>Déconnexion</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
