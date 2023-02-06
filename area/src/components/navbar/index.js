import React, {useState} from 'react'
import { useEffect } from 'react'
import { FaBars } from 'react-icons/fa'
import { Nav, NavbarContainer, NavLogo, MobileIcon, NavMenu, NavItem, NavLinks, NavBtn, NavBtnLink } from './navbarElements'
import { IconContext } from 'react-icons/lib'
import { animateScroll as scroll } from 'react-scroll'

const Navbar = ({ toggle, changeY, defaultState }) => {
  const [scrollNav, setScrollNav] = useState(defaultState)

  const changeNav = () => {
    if (window.scrollY >= changeY) {
      setScrollNav(false)
    } else {
      setScrollNav(true)
    }
  }

  useEffect(() => {
    window.addEventListener('scroll', changeNav)
  }, [])

  const toggleHome = () => {
    scroll.scrollToTop();
  }

  return (
    <>
    <IconContext.Provider value={{ color: '#f9f9f9' }}>
      <Nav scrollNav={scrollNav}>
        <NavbarContainer>
          <NavLogo to="/" onClick={toggleHome} top="15px" left="20px" fontsize="40px" >Hapilink</NavLogo>
          <MobileIcon onClick={toggle}>
            <FaBars />
          </MobileIcon>
          <NavMenu>
            <NavItem>
            <NavLogo to="/settings" onClick={toggleHome} top="23px" left="1600px" fontsize="20px" >Settings</NavLogo>
            </NavItem>
          </NavMenu>
          <NavBtn>
            <NavBtnLink to="/sign">Sign In</NavBtnLink>
          </NavBtn>
        </NavbarContainer>
      </Nav>
    </IconContext.Provider>
    </>
  )
}

export default Navbar