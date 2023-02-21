import styled from "styled-components";

export const RectangleArea = styled.div`
  position: fixed;
  display:flex;
  flex-direction: column;
  width: 120%;
  height: 100%;
  z-index: 0;
  background-color: #ebebeb;
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
  padding: none;
  opacity: 0.5;
  border: none;
  transition: all 0.1s ease-in-out;

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
  width: 1%;
  height: 50%;
  left: 80.5%;
  top: 25%;
  background: #373b48;
  border-radius: 10px 0px 0px 10px;
`;

export const BinRight = styled.div`
  position: absolute;
  width: 0.8%;
  height: 50%;
  left: 81%;
  top: 25%;
  background: #373b48;
  border-radius: 0px 10px 10px 0px;
`;

export const BinWhite = styled.div`
  position: absolute;
  width: 5%;
  height: 50%;
  left: 81.8%;
  top: 25%;
  background: #ebebeb;
`;