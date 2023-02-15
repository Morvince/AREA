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
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
`;

export const RectangleWrapper = styled.div`
    width: 100%;
    height: 100%;
    position: relative;
    display: flex;
    flex-direction: right;
`;

export const AutomationText = styled.div`
    width: 90%;
    height: 100%;
    position: relative;
    border-radius: 25px;
    font-family: 'Calistoga', cursive;
    font-size: 18px;
    font-weight: 600;
    color: #ffffff;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top : 25px;
    padding-bottom : 15px;
`;

export const LogoWrapper = styled.div`
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
`;

export const AutomationRectangle = styled.div`
    width: 50px;
    height: 40px;
    /* padding-left: 10px; */
    padding-bottom: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
`;

export const ArrowRectangle = styled.div`
    width: 30px;
    height: 30px;
    border-radius: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
`;

export const CircleArcBot = styled.div`
    width: 60px;
    height: 30px;
    border-radius: 0 0 50px 50px;
    box-shadow: 5px 5px 1px 0 #373b48;
    background: ${props => props.background};
    position: absolute;
    top: 125px;
    left: 50px;
`;

export const CircleArcTop = styled.div`
    width: 60px;
    height: 30px;
    border-radius: 0 0 50px 50px;
    background: ${props => props.background};
    position: absolute;
    top: 0px;
    left: 50px;
`;