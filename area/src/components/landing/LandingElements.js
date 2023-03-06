import styled from "styled-components";
import { Link, Link as LinkR } from 'react-router-dom';

export const Rect = styled.div`
    width: 100%;
    height: ${props => props.height};
    top: ${props => props.top};
    position: absolute;
    background: ${props => props.color};
`;

const StyledSvg = styled.svg`
  position: absolute;
  top: ${props => props.top};
  height: ${props => props.height};
  width: 100%;
  
  `;

const StyledPath = styled.path`
  fill: ${props => props.color};
`;

export const FirstWave = ({ top, height, color }) => {
  return (
    <StyledSvg viewBox="0 0 900 100" top={top} height={height}>
      <StyledPath
        d="M0,0V75c.5.5,94.5-25,250-25,144,0,254.3,49.5,400,50,137.5.5,250.5-24.5,250-25S899.5.5,900,0,92.5,0,0,0Z"
        transform="scale(1, -1)"
        color={color}
      />
    </StyledSvg>
  );
};

export const SecondWave = ({ top, height, color }) => {
  return (
    <StyledSvg viewBox="0 0 900 100" top={top} height={height}>
      <StyledPath
        d="M0,0V75c.5.5,94.5-25,250-25,144,0,254.3,49.5,400,50,137.5.5,250.5-24.5,250-25S899.5.5,900,0,92.5,0,0,0Z"
        color={color}
      />
    </StyledSvg>
  );
};

export const Text = styled.div`
    position: absolute;
    line-height: ${props => props.lineheight};
    text-align: center;
    font-size: ${props => props.fontsize};
    top: ${props => props.top};
    left: ${props => props.left};
    color: ${props => props.color};
    font-weight: ${props => props.fontweight};
    user-select: none;
`;

export const ButtonWithBg = styled(LinkR)`
    position: absolute;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    color: white;
    background-color : #560bad;
    height: ${props => props.height};
    width: ${props => props.width};
    top: ${props => props.top};
    left: ${props => props.left};
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 50px;
    transition: all 0.3s ease-in-out;
    &:hover {
      background: #b5179e;
      color: white;
      cursor: pointer;
    }
`;

export const ButtonTryIt = styled(LinkR)`
    position: absolute;
    display: inline-block;
    text-align: center;
    font-size: 30px;
    line-height: 75px;
    color: white;
    background-color : #560bad;
    height: ${props => props.height};
    width: ${props => props.width};
    top: ${props => props.top};
    left: ${props => props.left};
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 50px;
    transition: all 0.3s ease-in-out;
    &:hover {
        background: #b5179e;
        color: white;
        cursor: pointer;
    }
`;

export const ButtonWithoutBg = styled(LinkR)`
    position: absolute;
    color: black;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    font-size: 20px;
    height: ${props => props.height};
    width: ${props => props.width};
    top: ${props => props.top};
    left: ${props => props.left};
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 50px;
    &:hover {
        color: #4361ee;
        cursor: pointer;
    }
`;

export const Shape = styled.div`
    width: ${props => props.width};
    height: ${props => props.height};
    top: ${props => props.top};
    left: ${props => props.left};
    position: absolute;
    background: ${props => props.color};
    border-radius: 50px;
`;

export const IconWrapper = styled.div`
  opacity: 0.7;
  transition: opacity 0.1s ease;

  &:hover {
    opacity: 1;
  }
`;

export const ButtonNewAccount = styled(LinkR)`
    position: absolute;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    color: white;
    background-color : #560bad;
    height: 50px;
    width: 190px;
    top: 1.5%;
    left: 88%;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 50px;
    transition: all 0.3s ease-in-out;
    &:hover {
        background: #b5179e;
        color: white;
    }
`;

export const ButtonLogin = styled(LinkR)`
    position: absolute;
    color: black;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    font-size: 20px;
    top: 3%;
    left: 80%;
    text-decoration: none;
    &:hover {
        color: #4361ee;
    }
`;

export const ButtonLogout = styled(LinkR)`
    position: absolute;
    color: black;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    font-size: 20px;
    top: 3%;
    left: 80%;
    text-decoration: none;
    &:hover {
        color: #4361ee;
    }
`;

export const YoutubeScreen = styled.div`
  position: absolute;
  top: 415%;
  left: 23%;
`;
