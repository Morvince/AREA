import styled from 'styled-components';

export const ServicesBarContainer = styled.div`
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #686f84;
  width: 150px;
  height: 80%;
  position: fixed;
  left: 0;
  z-index: 999;
  border-radius: 0 50px 50px 0;
  margin-top: 50px;
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
  cursor: pointer;
  transition: all 0.2s ease-in-out;

  &:hover {
    transform: scale(1.2);
    transition: all 0.2s ease-in-out;
    cursor: pointer;
  }

  &:active {
    transform: scale(50.0);
    transition: all 0.2s ease-in-out;
  }

`;

