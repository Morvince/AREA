import styled from 'styled-components';

export const Rectangle= styled.div`
  display: inline-block;
  width: 250px;
  height: 150px;
  position: absolute;
  top: ${props => props.top};
  left:  ${props => props.left};
  right: ${props => props.right};
  bottom: ${props => props.bottom};
  background: #784ecc;
  border-radius: 25px; 
  `;