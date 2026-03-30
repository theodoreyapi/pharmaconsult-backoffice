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
            @if (Auth::user()->role == 'ADMIN' ||
                    Auth::user()->role == 'SUPERADMIN' ||
                    Auth::user()->role == 'PHARMACIEN')
                <li
                    class="dropdown {{ Route::is('index') ? 'open' : '' }}{{ Route::is('pharma-index') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Tableau de bord</span>
                    </a>
                    @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                        <ul class="sidebar-submenu {{ Route::is('index') ? 'show' : '' }}">
                            <li class="{{ Route::is('index') ? 'active-page' : '' }}">
                                <a href="{{ url('index') }}" class="{{ Route::is('index') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                    Tableau de bord</a>
                            </li>
                        </ul>
                    @endif
                    @if (Auth::user()->role == 'PHARMACIEN')
                        <ul class="sidebar-submenu {{ Route::is('pharma-index') ? 'show' : '' }}">
                            <li class="{{ Route::is('index') ? 'active-page' : '' }}">
                                <a href="{{ url('pharma-index') }}"
                                    class="{{ Route::is('pharma-index') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
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
                        <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                        <span>Utilisateurs</span>
                    </a>
                    <ul
                        class="sidebar-submenu {{ Route::is('users', 'user-add', 'view-profile') ? 'show' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'show' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'show' : '' }}">
                        <li
                            class="{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}">
                            <a href="{{ url('users') }}"
                                class="{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}{{ Route::is('users', 'user-add', 'view-profile') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Utilisateurs</a>
                        </li>
                    </ul>
                </li>
            @endif
            <li
                class="dropdown {{ Route::is('assurance', 'pharmacy', 'garde', 'commune', 'medicament', 'requete', 'reponse', 'reservation', 'view-pharmacy', 'add-pharmacy', 'paiement') ? 'open' : '' }}">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
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
                         "><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Pharmacies</a>
                        </li>
                        <li class="{{ Route::is('garde') ? 'active-page' : '' }}">
                            <a href="{{ url('garde') }}" class="{{ Route::is('garde') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-warning-main w-auto"></i>
                                Pharmacie de garde</a>
                        </li>
                        <li class="{{ Route::is('assurance') ? 'active-page' : '' }}">
                            <a href="{{ url('assurance') }}"
                                class="{{ Route::is('assurance') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i>
                                Assurances</a>
                        </li>
                        <li class="{{ Route::is('paiement') ? 'active-page' : '' }}">
                            <a href="{{ url('paiement') }}"
                                class="{{ Route::is('paiement') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i>
                                Moyen de paiement</a>
                        </li>
                        <li class="{{ Route::is('commune') ? 'active-page' : '' }}">
                            <a href="{{ url('commune') }}" class="{{ Route::is('commune') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-danger-main w-auto"></i>
                                Communes / Villes</a>
                        </li>
                        <li class="{{ Route::is('medicament') ? 'active-page' : '' }}">
                            <a href="{{ url('medicament') }}"
                                class="{{ Route::is('medicament') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-danger-main w-auto"></i>
                                Médicaments</a>
                        </li>
                    @endif
                    @if (Auth::user()->role != 'ADMIN' || Auth::user()->role != 'SUPERADMIN')
                        @if (Auth::user()->role == 'PHARMACIEN' || Auth::user()->role == 'GESTIONNAIRE')
                            <li class="{{ Route::is('requete') ? 'active-page' : '' }}">
                                <a href="{{ url('requete') }}"
                                    class="{{ Route::is('requete') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-danger-main w-auto"></i>
                                    Requêtes</a>
                            </li>
                            <li class="{{ Route::is('reponse') ? 'active-page' : '' }}">
                                <a href="{{ url('reponse') }}"
                                    class="{{ Route::is('reponse') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-danger-main w-auto"></i>
                                    Reponses</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN' || Auth::user()->role == 'GESTIONNAIRE')
                            <li class="{{ Route::is('reservation') ? 'active-page' : '' }}">
                                <a href="{{ url('reservation') }}"
                                    class="{{ Route::is('reservation') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-danger-main w-auto"></i>
                                    Réservations</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN')
                            <li class="{{ Route::is('rechargement') ? 'active-page' : '' }}">
                                <a href="{{ url('rechargement') }}"
                                    class="{{ Route::is('rechargement') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-success-main w-auto"></i>
                                    Rechargements</a>
                            </li>
                        @endif
                        @if (Auth::user()->role == 'PHARMACIEN' || Auth::user()->role == 'CAISSIERE')
                            <li class="{{ Route::is('transactions') ? 'active-page' : '' }}">
                                <a href="{{ url('transactions') }}"
                                    class="{{ Route::is('transactions') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-success-main w-auto"></i>
                                    Transactions</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
            @if (Auth::user()->role == 'SUPERADMIN')
                <li class="dropdown {{ Route::is('publicites') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
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

                {{--  <li
                    class="dropdown {{ Route::is('transaction', 'abonnement', 'utilisateur', 'pharmacies') ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="fe:vector" class="menu-icon"></iconify-icon>
                        <span>Rapports</span>
                    </a>
                    <ul
                        class="sidebar-submenu {{ Route::is('transaction', 'abonnement', 'utilisateur', 'pharmacies') ? 'show' : '' }}">
                        <li class="{{ Route::is('transaction') ? 'active-page' : '' }}">
                            <a href="transaction" class="{{ Route::is('transaction') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                Transactions</a>
                        </li>
                        <li class="{{ Route::is('abonnement') ? 'active-page' : '' }}">
                            <a href="abonnement" class="{{ Route::is('abonnement') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-warning-main w-auto"></i>
                                Abonnements</a>
                        </li>
                        <li class="{{ Route::is('utilisateur') ? 'active-page' : '' }}">
                            <a href="utilisateur" class="{{ Route::is('utilisateur') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-info-main w-auto"></i>
                                Utilisateurs</a>
                        </li>
                        <li class="{{ Route::is('pharmacies') ? 'active-page' : '' }}">
                            <a href="pharmacies" class="{{ Route::is('pharmacies') ? 'active-page' : '' }}"><i
                                    class="ri-circle-fill circle-icon text-danger-main w-auto"></i>
                                Pharmacies</a>
                        </li>
                    </ul>
                </li> --}}
            @endif
            @if (Auth::user()->role == 'ADMIN' || Auth::user()->role == 'SUPERADMIN')
                <li class="sidebar-menu-group-title">Paramètres</li>
                <li class="{{ Route::is('qrcode') ? 'active-page' : '' }}">
                    <a href="{{ url('qrcode') }}" class="{{ Route::is('qrcode') ? 'active-page' : '' }}">
                        <iconify-icon icon="hugeicons:qr-code" class="menu-icon"></iconify-icon>
                        <span>QrCode Pharmacie</span>
                    </a>
                </li>
                <li class="{{ Route::is('pricing') ? 'active-page' : '' }}">
                    <a href="{{ url('pricing') }}" class="{{ Route::is('pricing') ? 'active-page' : '' }}">
                        <iconify-icon icon="hugeicons:money-send-square" class="menu-icon"></iconify-icon>
                        <span>Forfaits</span>
                    </a>
                </li>
                {{-- <li>
                <a href="faq">
                    <iconify-icon icon="mage:message-question-mark-round" class="menu-icon"></iconify-icon>
                    <span>FAQs.</span>
                </a>
            </li> --}}
                <li
                    class="{{ Route::is('terms-about') ? 'active-page' : '' }}
            {{ Route::is('add-about') ? 'active-page' : '' }}
             {{ Route::is('edit-about') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-about') }}"
                        class="{{ Route::is('terms-about') ? 'active-page' : '' }}
                {{ Route::is('add-about') ? 'active-page' : '' }}
                 {{ Route::is('edit-about') ? 'active-page' : '' }}">
                        <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                        <span>Apropos de nous</span>
                    </a>
                </li>
                <li
                    class="{{ Route::is('terms-politicy') ? 'active-page' : '' }}
            {{ Route::is('add-politicy') ? 'active-page' : '' }}
            {{ Route::is('edit-politicy') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-politicy') }}"
                        class="{{ Route::is('terms-politicy') ? 'active-page' : '' }}
                    {{ Route::is('add-politicy') ? 'active-page' : '' }}
                    {{ Route::is('edit-politicy') ? 'active-page' : '' }}">
                        <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                        <span>Politique de confidentialite</span>
                    </a>
                </li>
                <li
                    class="{{ Route::is('terms-mention') ? 'active-page' : '' }}
            {{ Route::is('add-mention') ? 'active-page' : '' }}
            {{ Route::is('edit-mention') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-mention') }}"
                        class="{{ Route::is('terms-mention') ? 'active-page' : '' }}
                {{ Route::is('add-mention') ? 'active-page' : '' }}
                {{ Route::is('edit-mention') ? 'active-page' : '' }}">
                        <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                        <span>Mention legales</span>
                    </a>
                </li>
                <li
                    class="{{ Route::is('terms-aide') ? 'active-page' : '' }}
            {{ Route::is('add-aide') ? 'active-page' : '' }}
            {{ Route::is('edit-aide') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-aide') }}"
                        class="{{ Route::is('terms-aide') ? 'active-page' : '' }}
                {{ Route::is('add-aide') ? 'active-page' : '' }}
                {{ Route::is('edit-aide') ? 'active-page' : '' }}">
                        <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                        <span>Aide</span>
                    </a>
                </li>
                <li
                    class="{{ Route::is('terms-condition') ? 'active-page' : '' }}
            {{ Route::is('add-condition') ? 'active-page' : '' }}
            {{ Route::is('edit-condition') ? 'active-page' : '' }}">
                    <a href="{{ url('terms-condition') }}"
                        class="{{ Route::is('terms-condition') ? 'active-page' : '' }}
                    {{ Route::is('add-condition') ? 'active-page' : '' }}
                    {{ Route::is('edit-condition') ? 'active-page' : '' }}">
                        <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                        <span>Conditions generales d'utilisation</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->role == 'SUPERADMIN' ||
                    Auth::user()->role == 'PHARMACIEN')
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
                                    Utilisateurs Admin</a>
                            </li>
                            <li class="{{ Route::is('user-pharma') ? 'active-page' : '' }}">
                                <a href="user-pharma" class="{{ Route::is('user-pharma') ? 'active-page' : '' }}"><i
                                        class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                                    Utilisateurs Pharmacien</a>
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
                        {{--  <li class="{{ Route::is('notification') ? 'active-page' : '' }}">
                        <a href="notification" class="{{ Route::is('notification') ? 'active-page' : '' }}"><i
                                class="ri-circle-fill circle-icon text-warning-main w-auto"></i>
                            Notification</a>
                    </li>
                    <li class="{{ Route::is('notification-alert') ? 'active-page' : '' }}">
                        <a href="notification-alert"
                            class="{{ Route::is('notification-alert') ? 'active-page' : '' }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i>
                            Notification
                            Alert</a>
                    </li>
                    <li class="{{ Route::is('payment-gateway') ? 'active-page' : '' }}">
                        <a href="payment-gateway" class="{{ Route::is('payment-gateway') ? 'active-page' : '' }}"><i
                                class="ri-circle-fill circle-icon text-info-main w-auto"></i>
                            Moyens de paiement</a>
                    </li> --}}
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</aside>
