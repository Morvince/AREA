import styled from 'styled-components';

export const LeftColumn = styled.div`
  /* position: absolute; */
  display: flex;
  width: 150px;
`;

export const ServicesBarContainer = styled.div`
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: ${props => props.color};
  top: 4%;
  width: 150px;
  height: 100%;
  left: 0;
  z-index: 999;
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
  margin: 12px;
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

export const RectangleContener = styled.div`
  position: absolute;
  width: 350px;
  height: 100%;
  z-index: 1;
  transition: all 0.3s ease-in-out;
  background-color: ${props => props.color};

  &.open {
    transform: translateX(200px);
  }

  &.closed {
    transform: translateX(-400px);
  }
`;

export const ButtonConnect = styled.button`
  position: absolute;
  left: 22%;
  top: 50%;
  border-radius: 30px;
  background: #D4D3DC;
  white-space: nowrap;
  padding: 10px 22px;
  color: black;
  font-size: 40px;
  outline: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  text-decoration: none;
  user-select: none;

  &:hover {
    transition: all 0.2s ease-in-out;
    background: black;
    color: #D4D3DC;
  }
`;