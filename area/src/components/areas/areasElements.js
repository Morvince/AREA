import styled from "styled-components";

export const BgColor = styled.div`
    width: 100%;
    height: 200%;
    position: fixed;
    background: #373b48;
`;

export const BlankZone = styled.div`
  width: 80%;
  height: 80%;
  position: fixed;
  top: 15%;
  left: 10%;
  background: white;
  display: flex;
  flex-direction: column;
`;

export const NumberOfAreasText = styled.div`
    position: absolute;
    font-size: 30px;
    top: 10%;
    left: 32.5%;
    color: black;
    user-select: none;
`;

export const AreaZone = styled.div`
  background: grey;
  position: relative;
  top: 25%;
  height: 10%;
  width: 99%;
  left: 0.5%;
  margin-bottom: 20px;
  border: 2px solid black;
`;

export const Container = styled.div`
  position: absolute;
  top: 110%;
  left: 0;
  width: 100%;
  height: 300px;
  background-color: grey;
  border: 2px solid black;
  padding: 10px;
`;

export const AreaName = styled.div`
    position: absolute;
    background-color: black;
    color: white;
    font-size: 50px;
    font-weight: bold;
    display: inline-block;
    user-select: none;
    padding: 10px;
    `;

export const ButtonEdit = styled.div`
    /* button */
    position: absolute;
    background-color: rgba(0, 0, 0, 0.6);
    height: 20%;
    width: 10%;
    left : 79%;
    top: 79%;
    
    /* text */
    color: white;
    text-align: center;
    font-size: 40px;
    font-weight: bold;
    display: inline-block;
    user-select: none;
    padding: 10px;

    &:hover {
    background-color: rgba(0, 0, 0, 1);
}
`;

export const ButtonDelete = styled.div`
    /* button */
    position: absolute;
    background-color: rgba(255, 12, 0, 0.5);
    height: 20%;
    width: 10%;
    left : 89.5%;
    top: 79%;
    
    /* text */
    color: white;
    text-align: center;
    font-size: 40px;
    font-weight: bold;
    display: inline-block;
    user-select: none;
    padding: 10px;

    &:hover {
    background-color: rgba(255, 12, 0, 1);
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