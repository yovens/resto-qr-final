import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

console.log("Echo CHECK:", window.Echo);

if (window.Echo && typeof window.Echo.channel === 'function') {

    window.Echo.channel('kitchen')
        .listen('NewOrder', (e) => {

            console.log("🔥 CLIENT ORDER RECEIVED", e);

            window.dispatchEvent(
                new CustomEvent('new-order', { detail: e })
            );

        });

} else {
    console.error("❌ Echo pa init byen");
}