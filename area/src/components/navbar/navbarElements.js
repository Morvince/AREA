import styled from 'styled-components';
import { Link as LinkR } from 'react-router-dom';

export const NavRectBg = styled.div`
    width: 100%;
    height: 80px;
    top: 0%;
    position: absolute;
    background: #D4D3DC;
`;

export const ButtonHapilink = styled(LinkR)`
    position: absolute;
    line-height: 1.2;
    text-align: center;
    font-size: 35px;
    top: 2%;
    left: 15%;
    color: black;
    font-weight: bold;
    text-decoration: none;
    &:hover {
        color: #4361ee;
    }
`;

export const ButtonCreate = styled(LinkR)`
    position: absolute;
    line-height: 25px;
    text-align: center;
    font-size: 20px;
    top: 3%;
    left: 35%;
    color: black;
    display: inline-block;
    text-decoration: none;
    &:hover {
        color: #4361ee;
    }
`;

export const ButtonAreas = styled(LinkR)`
    position: absolute;
    color: black;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    font-size: 20px;
    top: 3%;
    left: 45%;
    text-decoration: none;
    &:hover {
        color: #4361ee;
    }
`;

export const ButtonDocumentation = styled(LinkR)`
    position: absolute;
    color: black;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    font-size: 20px;
    top: 3%;
    left: 55%;
    text-decoration: none;
    &:hover {
        color: #4361ee;
    }
`;