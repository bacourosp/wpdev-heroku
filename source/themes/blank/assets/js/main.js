/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( () => {
  const container = document.getElementById( 'site-navigation' )
  if ( ! container ) {
    return
  }

  const button = container.getElementsByTagName( 'button' )[ 0 ]
  if ( 'undefined' === typeof button ) {
    return
  }

  const menu = container.getElementsByTagName( 'ul' )[ 0 ]

  // Hide menu toggle button if menu is empty and return early.
  if ( 'undefined' === typeof menu ) {
    button.style.display = 'none'
    return
  }

  menu.setAttribute( 'aria-expanded', 'false' )
  if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
    menu.className += ' nav-menu'
  }

  button.onclick = function() {
    if ( -1 !== container.className.indexOf( 'toggled' ) ) {
      container.className = container.className.replace( ' toggled', '' )
      button.setAttribute( 'aria-expanded', 'false' )
      menu.setAttribute( 'aria-expanded', 'false' )
    } else {
      container.className += ' toggled'
      button.setAttribute( 'aria-expanded', 'true' )
      menu.setAttribute( 'aria-expanded', 'true' )
    }
  }

  // Get all the link elements within the menu.
  const links = menu.getElementsByTagName( 'a' )

  // Each time a menu link is focused or blurred, toggle focus.
  for ( const link of links ) {
    link.addEventListener( 'focus', toggleFocus, true )
    link.addEventListener( 'blur', toggleFocus, true )
  }

  /**
   * Sets or removes .focus class on an element.
   */
  function toggleFocus() {
    let self = this

    // Move up through the ancestors of the current link until we hit .nav-menu.
    while ( -1 === self.className.indexOf( 'nav-menu' ) ) {
      // On li elements toggle the class .focus.
      if ( 'li' === self.tagName.toLowerCase() ) {
        if ( -1 !== self.className.indexOf( 'focus' ) ) {
          self.className = self.className.replace( ' focus', '' )
        } else {
          self.className += ' focus'
        }
      }

      self = self.parentElement
    }
  }

  /**
   * Toggles `focus` class to allow submenu access on tablets.
   */
  ( () => {
    const parentLinks = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' )

    if ( 'ontouchstart' in window ) {
      const touchStartFn = ( e ) => {
        const menuItems = this.parentNode

        if ( ! menuItems.classList.contains( 'focus' ) ) {
          e.preventDefault()

          for ( const menuItem of menuItems.parentNode.children ) {
            if ( menuItems === menuItem ) {
              continue
            }

            menuItem.classList.remove( 'focus' )
          }

          menuItems.classList.add( 'focus' )
        } else {
          menuItems.classList.remove( 'focus' )
        }
      }

      for ( const parentLink of parentLinks ) {
        parentLink.addEventListener( 'touchstart', touchStartFn, false )
      }
    }
  } )()
} )()
