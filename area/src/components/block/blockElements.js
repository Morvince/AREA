import styled from "styled-components";

export const RectangleBlock = styled.div`
    width: 250px;
    height: 125px;
    position: absolute;
    top: ${props => props.top}px;
    left: ${props => props.left}px;
    background: ${props => props.color};
    border-radius: 25px;
    box-shadow: 5px 5px 1px 0 #373b48;
`;
