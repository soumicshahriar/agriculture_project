// Sidebar Toggle
const toggleBtn = document.getElementById('toggle-btn');
const sidebar = document.getElementById('sidebar');
const content = document.querySelector('.content');

// Toggle Sidebar
toggleBtn.addEventListener('click', () => {
 if (window.innerWidth <= 768) {
  // Small screens: Slide the sidebar in/out
  sidebar.classList.toggle('open');
 } else {
  // Large screens: Collapse/expand the sidebar
  sidebar.classList.toggle('collapsed');
  content.classList.toggle('shift');
 }
});

// Adjust sidebar behavior on window resize
window.addEventListener('resize', () => {
 if (window.innerWidth > 768) {
  // Ensure the sidebar remains open on larger screens
  sidebar.classList.remove('open');
 } else {
  // Ensure the sidebar is not collapsed on smaller screens
  sidebar.classList.remove('collapsed');
  content.classList.remove('shift');
 }
});
