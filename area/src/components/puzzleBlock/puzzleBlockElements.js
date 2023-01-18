import styled from "styled-components";

export const Rectangle = styled.div`
    width: 250px;
    height: 125px;
    position: absolute;
    top: ${position => position.y};
    left: ${position => position.x};
    background: ${props => props.color};
    border-radius: 25px;
    /* border: 3px solid black; */
    box-shadow: 5px 5px 1px 0 #373b48
;
`;

export const Barre = styled.div`
    height: 10000px;
    width: 5px;
    position: absolute;
    top: 0px;
    left: 600px;
    background: black
`;