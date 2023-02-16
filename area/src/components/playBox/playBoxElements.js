import styled from "styled-components";

export const RectangleArea = styled.div`
  position: fixed;
  display:flex;
  flex-direction: column;
  width: 120%;
  height: 100%;
  z-index: 0;
`;

export const MovableBox = styled.div`
  position: relative;
  display: flex;
  flex-grow: 2;
  border-radius: 25px;
  box-shadow: 5px 5px 1px 0 #373b48;
`;

export const TickButton = styled.button`
  position: absolute;
  top: 300px;
  left: 300px;
  background-color: #373b48;
  border: none;
  border-radius: 50%;
`;

export const ValidateButton = styled.button`
  position: absolute;
  top: 89%;
  left: 78%;
  cursor: pointer;
  opacity: 0.5;
  border: none;
  background: white;
  transition: all 0.1s ease-in-out;
  z-index: 100000;

  &.green {
    cursor: pointer;
  }

  &.red {
    cursor: not-allowed;
  }

  &:hover {
    opacity: 1;
  }
`;

export const BinLeft = styled.div`
  position: absolute;
  width: 15px;
  height: 500px;
  left: 1860px;
  top: 250px;
  background: #373b48;
  border-radius: 10px 0px 0px 10px;
`;

export const BinRight = styled.div`
  position: absolute;
  width: 20px;
  height: 500px;
  left: 1870px;
  top: 250px;
  background: #373b48;
  border-radius: 0px 10px 10px 0px;
  transition: all 0.3s ease-in-out;

  &:hover {
    left: 1825px;
    width: 66px;
    border-radius: 10px;
  }
`;

export const BinWhite = styled.div`
  position: absolute;
  width: 150px;
  height: 500px;
  left: 1890px;
  top: 250px;
  background: white;

`;