import styled from "styled-components";

export const RectangleArea = styled.div`
  position: fixed;
  display: flex;
  flex-direction: column;
  width: 120%;
  top : 80px;
  height: calc(100% - 80px);
  z-index: 0;
  background-color: #ebebeb;
  user-select: none;
`;

export const MovableBox = styled.div`
  position: relative;
  display: flex;
  height: 100%;
  width: 120%;
  left: 150px;
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

export const SaveNamePannel = styled.div`
  position: absolute;
  width: 25%;
  height: 7%;
  top: 45%;
  left: 33%;
  background-color: #D4D3DC;
  border: 3px solid black;

  color: black;
  font-size: 150%;
  font-weight: bold;
  display: flex; 
  align-items: center; 
  justify-content: left;
  user-select: none;
  padding: 10px;
`

export const CheckButton = styled.div`
  position: absolute;
  height: 100%;
  width: 15%;
  left: 85%;
  background-color: rgba(0, 128, 0, 0.7);
  border-left: 3px solid black;

  &:hover {
    cursor: pointer;
    background-color: green;
  }
`

export const WrittingZone  =styled.div`
  /* Zone :*/
  position: absolute;
  height: 84%;
  width: 63%;
  top: 8%;
  left: 20%;
  background-color: white;

  /* Text :  */
  color: black;
  text-align: left;
  white-space: nowrap;
  caret-color: black; /* Ajout */
  font-size: 150%;
  font-weight: bold;
  display: flex; 
  user-select: none;
  padding: 10px;
`;