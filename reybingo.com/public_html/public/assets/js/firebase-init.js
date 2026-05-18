// Inicialización de Firebase y gestión de notificaciones

// Configuración de Firebase
const firebaseConfig = {
  apiKey: "AIzaSyDn20f_8nVHeU_XzGDAbv2OOLfyplFKXIk",
  authDomain: "bingofamily-5a09a.firebaseapp.com",
  projectId: "bingofamily-5a09a",
  storageBucket: "bingofamily-5a09a.firebasestorage.app",
  messagingSenderId: "346461010889",
  appId: "1:346461010889:web:b5a8c5beeb57ec69a18618"
};

// Clase para gestionar notificaciones Firebase
class FirebaseNotificationManager {
  constructor() {
    this.initialized = false;
    this.token = null;
    this.messaging = null;
    this.init();
  }

  async init() {
    try {
      // Cargar Firebase desde CDN si no está cargado
      await this.loadFirebaseScripts();
      
      // Inicializar Firebase
      firebase.initializeApp(firebaseConfig);
      this.messaging = firebase.messaging();
      
      // Configurar Service Worker
      if ('serviceWorker' in navigator) {
        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
          scope: '/'
        });
        
        this.messaging.useServiceWorker(registration);
        console.log('✅ Firebase: Service Worker registrado');
      }
      
      // Verificar permisos existentes
      await this.checkPermission();
      
      this.initialized = true;
      console.log('✅ Firebase: Inicializado correctamente');
      
      // Manejar mensajes en primer plano
      this.setupForegroundNotifications();
      
    } catch (error) {
      console.error('❌ Firebase: Error de inicialización', error);
    }
  }

  async loadFirebaseScripts() {
    // Verificar si Firebase ya está cargado
    if (window.firebase) return Promise.resolve();
    
    // Cargar scripts necesarios
    const scripts = [
      'https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js',
      'https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js'
    ];
    
    const promises = scripts.map(src => {
      return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
      });
    });
    
    return Promise.all(promises);
  }

  async checkPermission() {
    try {
      // Verificar si ya tenemos token
      const currentToken = await this.messaging.getToken();
      
      if (currentToken) {
        this.token = currentToken;
        console.log('✅ Firebase: Token existente');
        this.saveTokenToServer(currentToken);
        return true;
      } else {
        console.log('⚠️ Firebase: No hay token disponible');
        return false;
      }
    } catch (error) {
      console.log('⚠️ Firebase: Error verificando permisos', error);
      return false;
    }
  }

  async requestPermission() {
    try {
      console.log('🔔 Firebase: Solicitando permiso...');
      
      // Solicitar permiso
      await Notification.requestPermission();
      
      // Obtener token
      const currentToken = await this.messaging.getToken();
      
      if (currentToken) {
        this.token = currentToken;
        console.log('✅ Firebase: Permiso concedido, token obtenido');
        
        // Guardar token en el servidor
        await this.saveTokenToServer(currentToken);
        
        // Mostrar notificación de prueba
        this.showWelcomeNotification();
        
        return true;
      } else {
        console.log('❌ Firebase: No se pudo obtener token');
        return false;
      }
    } catch (error) {
      console.error('❌ Firebase: Error solicitando permiso', error);
      return false;
    }
  }

  setupForegroundNotifications() {
    // Manejar mensajes cuando la app está abierta
    this.messaging.onMessage((payload) => {
      console.log('📬 Mensaje recibido en primer plano:', payload);
      
      // Mostrar notificación personalizada
      const title = payload.notification.title || 'Nueva notificación';
      const options = {
        body: payload.notification.body || 'Tienes una nueva notificación',
        icon: '/assets/img/logo.png',
        badge: '/assets/img/badge.png',
        data: payload.data || {}
      };
      
      // Mostrar notificación
      this.showCustomNotification(title, options);
    });
  }

  showCustomNotification(title, options) {
    // Mostrar notificación en la página
    const notificationElement = document.createElement('div');
    notificationElement.className = 'custom-notification';
    notificationElement.innerHTML = `
      <div class="notification-content">
        <div class="notification-icon">
          <img src="${options.icon || '/assets/img/logo.png'}" alt="Icono">
        </div>
        <div class="notification-text">
          <h4>${title}</h4>
          <p>${options.body}</p>
        </div>
        <button class="notification-close">×</button>
      </div>
    `;
    
    // Estilos
    notificationElement.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      max-width: 350px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 8px;
      z-index: 9999;
      overflow: hidden;
      animation: slideIn 0.3s forwards;
    `;
    
    // Agregar al DOM
    document.body.appendChild(notificationElement);
    
    // Cerrar al hacer clic
    notificationElement.querySelector('.notification-close').addEventListener('click', () => {
      notificationElement.style.animation = 'slideOut 0.3s forwards';
      setTimeout(() => {
        notificationElement.remove();
      }, 300);
    });
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
      if (document.body.contains(notificationElement)) {
        notificationElement.style.animation = 'slideOut 0.3s forwards';
        setTimeout(() => {
          notificationElement.remove();
        }, 300);
      }
    }, 5000);
    
    // También mostrar notificación nativa si está en segundo plano
    if (document.visibilityState !== 'visible') {
      if (Notification.permission === 'granted') {
        const notification = new Notification(title, options);
        
        notification.onclick = function() {
          window.focus();
          notification.close();
        };
      }
    }
  }

  async saveTokenToServer(token) {
    try {
      const response = await fetch('/notification/save-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ token })
      });
      
      const data = await response.json();
      console.log('✅ Firebase: Token guardado en servidor', data);
      return true;
    } catch (error) {
      console.error('❌ Firebase: Error guardando token', error);
      return false;
    }
  }

  showWelcomeNotification() {
    this.showCustomNotification(
      '🎉 ¡Notificaciones activadas!',
      {
        body: 'Ahora recibirás alertas de nuevas partidas de bingo',
        icon: '/assets/img/logo.png'
      }
    );
  }
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
  // Crear estilos para animaciones
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }
    
    .custom-notification .notification-content {
      display: flex;
      padding: 15px;
      align-items: center;
    }
    
    .custom-notification .notification-icon {
      width: 40px;
      height: 40px;
      margin-right: 10px;
    }
    
    .custom-notification .notification-icon img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
    
    .custom-notification .notification-text {
      flex: 1;
    }
    
    .custom-notification .notification-text h4 {
      margin: 0 0 5px;
      font-size: 16px;
      font-weight: bold;
    }
    
    .custom-notification .notification-text p {
      margin: 0;
      font-size: 14px;
      color: #666;
    }
    
    .custom-notification .notification-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #999;
      padding: 0 5px;
    }
  `;
  document.head.appendChild(style);
  
  // Inicializar gestor de notificaciones
  window.firebaseNotifications = new FirebaseNotificationManager();
});

// Función global para activar notificaciones
function activarNotificaciones() {
  if (window.firebaseNotifications) {
    window.firebaseNotifications.requestPermission();
  } else {
    console.error('Firebase no está inicializado');
  }
}
