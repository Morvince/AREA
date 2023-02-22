import styled from "styled-components";

export const BgColor = styled.div`
    width: 100%;
    height: 200%;
    position: fixed;
    background: #373b48;
`;


export const AreaZone = styled.div`
  display: flex;
  align-items: center;
  position: absolute;
  background: grey;
  height: 100px;
  width: 100%;
  border: 2px solid black;
  top: ${props => props.top};
`;

export const GlobalContainer = styled.div`
  width: 90%;
  height: ${props => props.height};
  top: 35%;
  left: 5%;
  position: absolute;
  margin-bottom: 100px;
  display: flex;
  flex-direction: column;
`;

export const NumberOfAreasText = styled.div`
  position: absolute;
  font-size: 30px;
  top: 20%;
  left: 35%;
  color: white;
  user-select: none;
  font-weight: bold;
`;

export const BoxContent = styled.div`
  position: absolute;
  display: flex;
  width: 100%;
  height: 400px;
  background: grey;
  margin-top: 620px;
  margin-bottom: 100px;
  border: 2px solid black;
  color: white;
`;

export const AreaName = styled.div`
  position: absolute;
  background-color: black;
  color: white;
  font-size: 50px;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;
  left: 0%;
`;

export const ButtonEdit = styled.div`
  /* button */
  position: absolute;
  background-color: rgba(0, 0, 0, 0.6);
  height: 20%;
  width: 10%;
  left : 80%;
  top: 80%;
  
  /* text */
  color: white;
  text-align: center;
  font-size: 40px;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;

  &:hover {
    background-color: rgba(0, 0, 0, 1);
    cursor: pointer;
  }
`;

export const ButtonDelete = styled.div`
  /* button */
  position: absolute;
  background-color: rgba(255, 12, 0, 0.5);
  height: 20%;
  width: 10%;
  left : 90%;
  top: 80%;
  
  /* text */
  color: white;
  text-align: center;
  font-size: 40px;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;

  &:hover {
    background-color: rgba(255, 12, 0, 1);
    cursor: pointer;
  }
`;

export const ArrowArea = styled.div`
  position: absolute;
  background-color: black;
  height: 100%;
  width: 5%;
  left : 95%;
  opacity: 0.7;

  &:hover {
      opacity: 1;
      cursor: pointer;
  }
`;