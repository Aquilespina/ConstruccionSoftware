
document.addEventListener('DOMContentLoaded', function() {
 const navItems = document.querySelectorAll('.nav-item');
  const modules = document.querySelectorAll('.module');
  const pageTitle = document.getElementById('pageTitle');
  
  navItems.forEach(item => {
    item.addEventListener('click', function() {
      const target = this.getAttribute('data-target');
      
     
      navItems.forEach(nav => nav.classList.remove('active'));
      this.classList.add('active');
      
     
      modules.forEach(module => {
        module.classList.remove('active');
        if (module.id === `mod-${target}`) {
          module.classList.add('active');
        }
      });
      
     
      const navLabel = this.querySelector('.nav-label').textContent;
      pageTitle.textContent = navLabel;
      
     
      if (window.innerWidth <= 1024) {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
      }
    });
  });
  
    
  const sidebar = document.getElementById('sidebar');
  const toggleSidebar = document.getElementById('toggleSidebar');
  const overlay = document.getElementById('overlay');
  
  toggleSidebar.addEventListener('click', function() {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
  });
  
  overlay.addEventListener('click', function() {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
  });
  
    
  const tabButtons = document.querySelectorAll('.tab-button');
  
  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      const parent = this.closest('.hospitalizations-container, .procedures-container');
      const tabs = parent.querySelectorAll('.tab-button');
      
      tabs.forEach(tab => tab.classList.remove('active'));
      this.classList.add('active');
      

    });
  });
  

  const dateNavs = document.querySelectorAll('.date-nav');
  const currentDate = document.querySelector('.current-date');
  
  dateNavs.forEach(nav => {
    nav.addEventListener('click', function() {
      
    });
  });
  
 
  const searchInput = document.querySelector('.search-input');
  
  searchInput.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    
    console.log('Searching for:', searchTerm);
  });
  
 
  const consultationForm = document.querySelector('.consultation-form');
  
  if (consultationForm) {
    consultationForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      console.log('Consultation form submitted');
    });
  }
  
 
  document.querySelector('.nav-item[data-target="home"]').click();
  

  window.addEventListener('resize', function() {
    if (window.innerWidth > 1024) {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    }
  });
});