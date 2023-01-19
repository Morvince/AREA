import styled from 'styled-components';

export const ServicesBarContainer = styled.div`
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #686f84;
  width: 150px;
  height: 87%;
  position: fixed;
  left: 0;
  z-index: 999;
  border-radius: 0 50px 50px 0;
  margin-top: 20px;
  transition: transform .2s ease-in-out;

  &.open {
    transform: translateX(0);
  }

  &.closed {
    transform: translateX(-150px);
  }
`;

export const ServicesName = styled.h1`
  color: #ebebeb;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  height: 20%;
`;

export const ServicesBarWrapper = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 60%;
  height: 65%;
  background-color: #ebebeb;
  border-radius: 10px;
  padding: 5px;
`;

export const IconBox = styled.div`
  margin: 4px;
  cursor: pointer;
  transition: all 0.2s ease-in-out;

  &:hover {
    transition: all 0.2s ease-in-out;
    transform: scale(1.2);
    cursor: pointer;
  }

  &:active {
    transition: all 0.1s ease-in-out;
    transform: scale(1.5);
  }
`;

export const SwitchSlider = styled.div`
  position: absolute;
  top: 50%;
  left: 150px;
  width: 20px;
  height: 60px;
  background-color: #9966ff;
  border-radius: 0 50px 50px 0;
  cursor: pointer;
  transition: transform .2s ease-in-out;

  &.open {
    transform: translateX(0);
  }

  &.closed {
    transform: translateX(-150px);
  }

  &:hover {
    background-color: green;
  }

  &:active {
    background-color: red;
  }
`;

