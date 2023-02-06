import styled from "styled-components";
import { Link as LinkR } from 'react-router-dom';

export const Rect = styled.div`
width: 100%;
height: ${props => props.height};
top: ${props => props.top};
position: absolute;
background: ${props => props.color};
`;

export const SettingsRect = styled.div`
    position: absolute;
    width: ${props => props.width};
    height: ${props => props.height};
    top: ${props => props.top};
    left: ${props => props.left};
    background: ${props => props.color};
    border-radius: 30px;
`;

export const Connect = styled.button`
  position: absolute;
  left: ${props => props.left};
  top: ${props => props.top};
  border-radius: 30px;
  background: #ebebeb;
  white-space: nowrap;
  padding: 10px 22px;
  color: black;
  font-size: 40px;
  outline: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  text-decoration: none;

  &:hover {
    transition: all 0.2s ease-in-out;
    background: black;
    color: #ebebeb;
  }
`;

export const Connected = styled.button`
  position: absolute;
  left: ${props => props.left};
  top: ${props => props.top};
  border-radius: 30px;
  background: #373b48;
  white-space: nowrap;
  padding: 10px 22px;
  color: black;
  font-size: 40px;
  outline: none;
  border: none;
  transition: all 0.2s ease-in-out;
  text-decoration: none;
`;