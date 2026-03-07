// firebase-messaging-sw.js
importScripts("https://www.gstatic.com/firebasejs/12.4.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging-compat.js");

firebase.initializeApp({
  apiKey: "AIzaSyC2KLacO40EHg9zFYDngKlZqll6nySDegI",
  authDomain: "pharmaconsults-f209a.firebaseapp.com",
  projectId: "pharmaconsults-f209a",
  storageBucket: "pharmaconsults-f209a.firebasestorage.app",
  messagingSenderId: "748172317271",
  appId: "1:748172317271:web:ae6fe6a21a61bce5968912"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log("📩 Notification reçue en arrière-plan :", payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon || "/favicon.ico"
  };
  self.registration.showNotification(notificationTitle, notificationOptions);
});

