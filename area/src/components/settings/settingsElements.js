import styled from "styled-components";
import { Link as LinkR } from 'react-router-dom';

export const Rect = styled.div`
width: 100%;
height: ${props => props.height};
top: ${props => props.top};
position: absolute;
background: ${props => props.color};
`;

export const SettingsRect = styled.div`
    position: absolute;
    width: ${props => props.width};
    height: ${props => props.height};
    top: ${props => props.top};
    left: ${props => props.left};
    background: ${props => props.color};
    border-radius: 30px;
`;