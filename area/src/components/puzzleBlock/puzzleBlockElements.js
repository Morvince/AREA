import styled from "styled-components";

export const Rectangle = styled.div`
    width: 250px;
    height: 125px;
    position: absolute;
    z-index: 10;
    top: ${position => position.y};
    left: ${position => position.x};
    background: ${props => props.color};
    border-radius: 25px;
    box-shadow: 5px 5px 1px 0 #373b48;
`;

export const Bin = styled.div`
    width: 30px;
    height: 600px;
    z-index: 5;
    position: absolute;
    top:  150px;
    left: 2040px;
    background: ${props => props.color};
    border-radius: 25% 0% 0% 25%;
`;

export const Bin2 = styled.div`
    width: 20px;
    height: 600px;
    z-index: 100;
    position: absolute;
    top:  150px;
    left: 2050px;
    background: ${props => props.color};
    border-radius: 25% 0% 0% 25%;
`;