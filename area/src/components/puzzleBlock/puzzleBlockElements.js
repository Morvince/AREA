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