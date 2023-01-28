import styled from "styled-components";

export const Rectangle = styled.div`
    width: 250px;
    height: 125px;
    position: absolute;
    top: ${props => props.top};
    left: ${props => props.left};
    background: ${props => props.color};
    border-radius: 25px;
    box-shadow: 5px 5px 1px 0 #373b48;
`;
