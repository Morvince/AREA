import React from 'react'
import {SidebarContainer, Icon, CloseIcon, SidebarWrapper, SidebarMenu, SideBtnWrap, SidebarLink, SidebarRoute} from './sidebarElements'

//  function that manage the SideBar if you are on mobile
const Sidebar = ({ isOpen, toggle}) => {
  return (
    <SidebarContainer isOpen={isOpen} onClick={toggle}>
      <Icon onClick={toggle}>
        <CloseIcon />
      </Icon>
      <SidebarWrapper>
        <SidebarMenu>
            <SidebarLink to='about' onClick={toggle}>About</SidebarLink>
            <SidebarLink to='signup' onClick={toggle}>Sign Up</SidebarLink>
        </SidebarMenu>
        <SideBtnWrap>
            <SidebarRoute to='/'>Sign In</SidebarRoute>
        </SideBtnWrap>
      </SidebarWrapper>
    </SidebarContainer>
  )
}

export default Sidebar
