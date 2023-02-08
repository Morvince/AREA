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

export const Button1 = styled(LinkR)`
    display: inline-block;
    position: absolute;
    color: white;
    text-align: center;
    font-size: 20px;
    top: ${props => props.top};
    left: ${props => props.left};
    font-family: Roboto;
    font-weight: bold;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 50px;
    transition: all 0.3s ease-in-out;
    &:hover {
        background: white;
        color: black;
    }
`;

export const Button2 = styled(LinkR)`
    display: inline-block;
    position: absolute;
    color: black;
    background: white;
    text-align: center;
    font-size: 40px;
    width: ${props => props.width};
    height: 90px;
    top: ${props => props.top};
    left: ${props => props.left};
    font-family: Roboto;
    font-weight: bold;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 50px;
    transition: all 0.3s ease-in-out;
    &:hover {
        background: black;
        color: white;
    }
`;