import styled from "styled-components";
import { Link as LinkR } from 'react-router-dom';

export const Rect = styled.div`
    width: 100%;
    height: ${props => props.height};
    top: ${props => props.top};
    position: absolute;
    background: ${props => props.color};
`;

export const Text = styled.div`
    position: absolute;
    line-height: ${props => props.lineheight};
    text-align: center;
    font-size: ${props => props.fontsize};
    top: ${props => props.top};
    left: ${props => props.left};
    font-family: Roboto;
    color: ${props => props.color};
    font-weight: ${props => props.fontweight};
`;

export const Navebar = styled.div` 
    position: absolute;
    height: 100px;
    width: 100%;
    background: black;
`;

export const Button = styled.div`
    position: absolute;
    height: 130px;
    width: 370px;
    top: ${props => props.top};
    left: 780px;
    background: white;
    border-radius: 50px;
   
`;
