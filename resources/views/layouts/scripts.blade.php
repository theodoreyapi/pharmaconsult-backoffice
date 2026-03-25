<!--
 <script type="module">
     import {
         initializeApp
     } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
     import {
         getMessaging,
         getToken,
         onMessage
     } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js";

     // Configuration Firebase de ton projet
     const firebaseConfig = {
         apiKey: "AIzaSyC2KLacO40EHg9zFYDngKlZqll6nySDegI",
         authDomain: "pharmaconsults-f209a.firebaseapp.com",
         projectId: "pharmaconsults-f209a",
         storageBucket: "pharmaconsults-f209a.firebasestorage.app",
         messagingSenderId: "748172317271",
         appId: "1:748172317271:web:ae6fe6a21a61bce5968912",
         measurementId: "G-63SSR08X1P"
     };

     // Initialisation de Firebase
     const app = initializeApp(firebaseConfig);
     const messaging = getMessaging(app);

     // Clé publique VAPID (à générer sur Firebase Cloud Messaging)
     const vapidKey = "BG6C8-yA_U0wTkNU6Axw9psozQar5rx9ojaytvEYBzT47xSjQbCRbdFUGX01Gwq4GI3sgPDgQ0ZfukI3OgPE30g";

     // Demande de permission pour recevoir les notifications
     Notification.requestPermission().then(async (permission) => {
         if (permission === "granted") {
             try {
                 const token = await getToken(messaging, {
                     vapidKey
                 });
                 console.log("✅ Token FCM obtenu :", token);

                 // Envoi du token au backend Laravel
                 await fetch("{{ url('save-fcm-token') }}", {
                     method: "POST",
                     headers: {
                         "Content-Type": "application/json",
                         "Accept": "application/json",
                         "X-CSRF-TOKEN": "{{ csrf_token() }}"
                     },
                     body: JSON.stringify({
                         userName: "{{ Auth::user()->email }}",
                         token: token
                     })
                 });
             } catch (error) {
                 console.error("❌ Erreur lors de la récupération du token :", error);
             }
         } else {
             console.warn("⚠️ Permission de notification refusée");
         }
     });

     // Ecoute les messages quand la page est ouverte
     onMessage(messaging, (payload) => {
         console.log("📩 Notification reçue :", payload);

         // Affiche une notification navigateur
         new Notification(payload.notification.title, {
             body: payload.notification.body,
             icon: payload.notification.icon || "/favicon.ico"
         });
     });

     // Enregistrement du service worker
     if ('serviceWorker' in navigator) {
         navigator.serviceWorker.register('/firebase-messaging-sw.js')
             .then((registration) => {
                 console.log('Service Worker enregistré:', registration);
                 messaging.useServiceWorker(registration);
             })
             .catch((err) => console.error('Erreur service worker:', err));
     }
 </script>
-->

 <!-- jQuery library js -->
 <script src="{{ URL::asset('') }}assets/js/lib/jquery-3.7.1.min.js"></script>
 <!-- Bootstrap js -->
 <script src="{{ URL::asset('') }}assets/js/lib/bootstrap.bundle.min.js"></script>
 <!-- Apex Chart js -->
 <script src="{{ URL::asset('') }}assets/js/lib/apexcharts.min.js"></script>
 <!-- Data Table js -->
 <script src="{{ URL::asset('') }}assets/js/lib/dataTables.min.js"></script>
 <!-- Iconify Font js -->
 <script src="{{ URL::asset('') }}assets/js/lib/iconify-icon.min.js"></script>
 <!-- jQuery UI js -->
 <script src="{{ URL::asset('') }}assets/js/lib/jquery-ui.min.js"></script>
 <!-- Vector Map js -->
 <script src="{{ URL::asset('') }}assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
 <script src="{{ URL::asset('') }}assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
 <!-- Popup js -->
 <script src="{{ URL::asset('') }}assets/js/lib/magnifc-popup.min.js"></script>
 <!-- Slick Slider js -->
 <script src="{{ URL::asset('') }}assets/js/lib/slick.min.js"></script>
 <!-- main js -->
 <script src="{{ URL::asset('') }}assets/js/app.js"></script>

 <script src="{{ URL::asset('') }}assets/js/homeOneChart.js"></script>

 @stack('scripts')
