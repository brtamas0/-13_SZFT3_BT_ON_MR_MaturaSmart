export function setTitle(to, from, next) {
  const appName = import.meta.env.VITE_APP_NAME || 'MaturaSmart'
  
  const title = to.meta.title 
      ? `${to.meta.title} | ${appName}`
      : appName

  document.title = title
  next()
}