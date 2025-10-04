window.addEventListener('scroll', () => {
  const bar = document.getElementById('loading-bar');
  const scrollTop = window.scrollY;
  const docHeight = document.documentElement.scrollHeight - window.innerHeight;
  const scrollPercent = (scrollTop / docHeight) * 100;
  bar.style.width = `${scrollPercent}%`;
});
