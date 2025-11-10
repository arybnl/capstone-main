// Sidebar.js

// toggle collapse kapag click sa logo
document.getElementById("logo").addEventListener("click", function() {
  // Only toggle collapse on desktop, not mobile (where it's an overlay)
  if (window.innerWidth > 1024) {
    document.querySelector(".sidebar").classList.toggle("collapsed");
  }
});

const sidebar = document.querySelector(".sidebar");
const menuToggle = document.getElementById("menuToggle");
// contentWrapper is not directly modified in JS for the overlay,
// but it's good practice to have it if you were to shift content.
// For the overlay, we create a new element.

// toggle dropdown menu kapag mobile hamburger menu
menuToggle.addEventListener("click", function() {
  sidebar.classList.toggle("active");
  // Optional: Add an overlay to the content when sidebar is active on mobile
  if (window.innerWidth <= 1024) {
      if (sidebar.classList.contains("active")) {
          // Create a clickable overlay to close the sidebar
          let overlay = document.createElement('div');
          overlay.id = 'sidebar-overlay';
          Object.assign(overlay.style, {
              position: 'fixed',
              top: 0,
              left: 0,
              width: '100%',
              height: '100%',
              backgroundColor: 'rgba(0,0,0,0.5)',
              zIndex: 1040, // Below sidebar, above content
              cursor: 'pointer'
          });
          document.body.appendChild(overlay);
          overlay.addEventListener('click', () => {
              sidebar.classList.remove("active");
              overlay.remove();
          });
      } else {
          // If sidebar is being closed, remove existing overlay
          document.getElementById('sidebar-overlay')?.remove();
      }
  }
});

// Close sidebar if window resized from mobile to desktop while active
window.addEventListener('resize', () => {
    if (window.innerWidth > 1024 && sidebar.classList.contains("active")) {
        sidebar.classList.remove("active");
        document.getElementById('sidebar-overlay')?.remove();
    }
});
