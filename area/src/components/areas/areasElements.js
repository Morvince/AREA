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
  border-radius: 30px;
`;

export const AreasZoneAction = styled.div`
  display: flex;
  align-items: center;
  position: absolute;
  height: 96%;
  width: 28%;
  left: 1%;
  top: 3%;
`;

export const AreasZoneReactions = styled.div`
  display: flex;
  align-items: center;
  position: absolute;
  height: 94%;
  width: 50%;
  left: 33%;
  overflow-x: auto;
  top: 3%;
`

export const AreasZoneReactionsMoovable = styled.div`
  display: flex;
  align-items: center;
  position: absolute;
  height: 90%;
  width: 100%;
  border: 3px solid red;
  top: 0%;
`

export const ServiceNameReaction = styled.div`
  display: flex;
  position: absolute;
  height: 20%;
  width: 100%;
  top: 5%;

  /* text */
  color: white;
  text-align: center;
  font-size: 200%;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;
`;

export const ServiceNameAction = styled.div`
  display: flex;
  position: absolute;
  height: 20%;
  width: 100%;
  top: 10%;

  /* text */
  color: white;
  text-align: center;
  font-size: 200%;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;
`;

export const NameAction = styled.div`
  display: flex;
  position: absolute;
  height: 70%;
  width: 100%;
  top: 40%;

  /* text */
  color: white;
  text-align: center;
  font-size: 200%;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;
`;

export const ValuesReaction = styled.div`
  display: flex;
  position: absolute;
  height: 50%;
  width: 100%;
  top: 50%;

  /* text */
  color: white;
  text-align: center;
  font-size: 200%;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;
`;

export const NameReaction = styled.div`
  display: flex;
  position: absolute;
  height: 70%;
  width: 100%;
  top: 25%;

  /* text */
  color: white;
  text-align: center;
  font-size: 200%;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 20px;
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
  left: 42%;
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
  border-radius: 30px;
`;

export const AreaName = styled.div`
  position: absolute;
  background-color: black;
  color: white;
  font-size: 200%;
  font-weight: bold;
  display: inline-block;
  user-select: none;
  padding: 30px;
  left: -0.1%;
  border-radius: 30px 0px 0px 30px;
`;

export const ButtonEdit = styled.div`
  /* button */
  position: absolute;
  background-color: rgba(0, 0, 0, 0.6);
  height: 100%;
  width: 7%;
  left : 86.1%;
  
  /* text */
  color: white;
  text-align: center;
  font-size: 200%;
  font-weight: bold;
  display: flex; 
  align-items: center; 
  justify-content: center;
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
height: 100%;
width: 7%;
left : 93.1%;
border-radius: 0px 30px 30px 0px;

/* text */
color: white;
text-align: center;
font-size: 200%;
font-weight: bold;
display: flex; 
align-items: center; 
justify-content: center;
user-select: none;
padding: 20px;

&:hover {
  background-color: rgba(255, 12, 0, 1);
  cursor: pointer;
}
`;

export const CutBarre = styled.div`
  position: absolute;
  background-color: black;
  height: 100%;
  width: 0.2%;
  left : 30%;
  top: 0%;
  user-select: none;
`;

export const ArrowArea = styled.div`
  position: absolute;
  background-color: black;
  height: 100%;
  width: 5%;
  left : 95.2%;
  opacity: 0.7;
  border-radius: 0px 30px 30px 0px;


  &:hover {
      opacity: 1;
      cursor: pointer;
  }
`;