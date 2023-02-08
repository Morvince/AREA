import styled from "styled-components";

export const RectangleArea = styled.div`
  position: fixed;
  display:felx;
  flex-direction: column;
  width: 100%;
  height: 100%;
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
