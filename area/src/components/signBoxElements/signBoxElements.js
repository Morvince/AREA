import styled, { keyframes } from 'styled-components'
import {darkGray, lightGray, lightPurple, darkPurple, white} from '../../color'

const animate = keyframes`
  0% { transform: rotate(0deg) }
  100% { transform: rotate(360deg) }
`;

export const SignContainer = styled.div`
  position: relative;
  justify-content: center;
  align-items: center;
  height: 70%;
  width: 28%;
  border-radius: 20px;
  overflow: hidden;
  background: ${lightGray};
  &::before {
    position: absolute;
    content: '';
    bottom: -50%;
    right: -50%;
    height: 100%;
    width: 100%;
    background: linear-gradient(180deg, transparent, ${lightPurple}, transparent);
    transform-origin: top left;
    animation: ${animate} 6s linear infinite;
  }
  &::after {
    position: absolute;
    content: '';
    top: -50%;
    left: -50%;
    height: 100%;
    width: 100%;
    background: linear-gradient(0deg, transparent, ${lightPurple}, transparent);
    transform-origin: bottom right;
    animation: ${animate} 6s linear infinite;
  }
`;

export const SignForm = styled.form`
  position: absolute;
  inset: 2.5px;
  border-radius: 20px;
  background: ${props => props.bg ? props.bg : white};
  margin-top: 0;
  margin-bottom: 0;
  padding: 0px 50px;
  z-index: 10;
  display: flex;
  flex-direction: column;
  transition-property: transform;
  transition-duration: 0.6s;
`;

export const InputSignContainer = styled.div`
  position: relative;
  padding-top: ${props => props.paddingTop};
  width: 100%;
  overflow: hidden;
`;

export const InputSignField = styled.input`
  position: relative;
  width: 100%;
  padding: 20px 10px 10px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 1.3em;
  color: ${white};
  letter-spacing: 0.05em;
  caret-color: ${props => props.caret ? props.caret : white};
  z-index: 10;
  &:valid ~ span,
  &:not(:placeholder-shown) ~ span,
  &:focus ~ span {
    color: ${lightPurple};
    transform: translateY(-30px);
    font-size: 0.80em;
  }
  &:valid ~ i,
  &:not(:placeholder-shown) ~ i,
  &:focus ~ i {
    height: 44px;
  }
`;

export const InputSignLabel = styled.span`
  position: absolute;
  left: 0;
  padding: 20px 0px 10px;
  font-size: 1em;
  color: ${darkGray};
  letter-spacing: 0.05em;
  pointer-events: none;
  white-space: nowrap;
  transition: 0.5s;
`;

export const InputSignLine = styled.i`
  position: absolute;
  left: 0;
  bottom: 0;
  height: 2px;
  width: 100%;
  background: ${lightPurple};
  border-radius: 4px;
  pointer-events: none;
  z-index: 9;
  transition: 0.5s;
`;

export const LinkSignContainer = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 0;
`;

export const LinkForgotPassword = styled.a`
  font-size: 0.90em;
  font-weight: bold;
  color: ${darkGray};
  text-decoration: none;
  &:hover {
    color: ${lightPurple};
  }
`;

export const LinkSignInOrUp = styled.p`
  color: ${darkPurple};
  font-size: 15px;
  text-decoration: underline;
  align-self: center;
  margin-top: 5px;
  cursor: pointer;
  &:hover {
    color: ${lightPurple};
  }
`;

export const LabelCheckboxField = styled.label`
  font-size: 0.90em;
  margin-left: 6px;
  font-weight: bold;
  color: ${darkPurple};
`;

export const CheckboxField = styled.input`
  width: 17px;
  height: 17px;
  &:hover {
    cursor: pointer;
  }
  &:hover ~ label {
    color: ${lightPurple};
  }
`;

export const SubmitButton = styled.input`
  width: 34%;
  border: none;
  outline: none;
  padding: 16px 0px;
  margin-top: ${props => props.mTop ? props.mTop : 0};
  align-self: center;
  background: ${lightPurple};
  font-size: 1em;
  font-weight: bold;
  border: 2px solid ${props => props.borderColor};
  border-radius: 50px;
  cursor: pointer;
  color: ${props => props.color ? props.color : white};
  box-shadow: inset 0 0 0 0 ${props => props.colorBS};
  transition-property: border-color, color, box-shadow;
  transition-timing-function: ease-out;
  transition-duration: 0.4s;
  &:hover {
    box-shadow: inset 170px 0 0 0 ${props => props.colorBS};
    color: ${props => props.colorTr ? props.colorTr : props.color};
    border-color: ${props => props.borderTrColor};
  }
  &:active {
    opacity: 0.9;
  }
`;

export const AccountButton = styled.button`
  height: 55px;
  width: 25%;
  background: ${lightPurple};
  border: none;
  border-radius: 0 0 55px 55px;
  align-self: center;
  cursor: pointer;
  transition-property: background-color;
  transition-timing-function: ease-out;
  transition-duration: 0.3s;
  &:hover {
    background-color: ${props => props.colorBG};
    border: 2px solid ${lightPurple};
  }
  &:active {
    opacity: 0.9;
  }
`;