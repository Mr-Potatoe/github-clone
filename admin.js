// JavaScript for Parallax Effect
document.addEventListener('scroll', () => {
    const layers = document.querySelectorAll('.parallax-layer');
    const scrollY = window.scrollY;

    layers.forEach(layer => {
        const speed = layer.getAttribute('data-speed');
        const yPos = scrollY * speed;
        layer.style.transform = `translate3d(0, ${yPos}px, 0)`;
    });
});
