import styled from "styled-components";

export const BgColor = styled.div`
    width: 100%;
    height: 100%;
    position: fixed;
    background: #373b48;
`;

export const IntroText = styled.div`
    position: absolute;
    line-height: ${props => props.lineheight};
    text-align: center;
    font-size: ${props => props.fontsize};
    top: ${props => props.top};
    left: ${props => props.left};
    color: ${props => props.color};
    font-weight: ${props => props.fontweight};
`;

export const ContainerLeft = styled.div`
    width: 40%;
    height: 100%;
    top: 40%;
    left: 5%;
    position: absolute;
    margin-bottom: 100px;
    display: flex;
    flex-direction: column;
`;

export const BoxContentLeft = styled.div`
    position: relative;
    display: flex;
    width: 100%;
    height: 60%;
    background: ${props => props.background};
    border-radius: 15px;
    margin-top: -95px;
    margin-bottom : 100px;
    opacity: 0.7;
    color : white;
`;

export const DiscordBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #5470d6;
    height: 30%;
    border-radius: 15px;
    opacity: 0.7;
    margin-bottom: 100px;
    cursor: pointer;

    &:hover {
        opacity: 1;
    }
`;

export const SpotifyBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #10a143;
    height: 30%;
    border-radius: 15px;
    opacity: 0.7;
    margin-bottom: 100px;
    cursor: pointer;

    &:hover {
        opacity: 1;
    }
`;

export const TwitchBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #c2134f;
    height: 30%;
    border-radius: 15px;
    opacity: 0.7;
    margin-bottom: 100px;
    cursor: pointer;

    &:hover {
        opacity: 1;
    }
`;

export const ContainerRight = styled.div`
    width: 40%;
    height: 100%;
    top: 40%;
    left: 55%;
    position: absolute;
    margin-bottom: 100px;
    display: flex;
    flex-direction: column;
`;

export const BoxContentRight = styled.div`
    position: relative;
    display: flex;
    width: 100%;
    height: 60%;
    background: ${props => props.background};
    border-radius: 15px;
    margin-top: -95px;
    margin-bottom : 100px;
    opacity: 0.7;
    color : white;
`;

export const GoogleBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #d92516;
    height: 30%;
    border-radius: 15px;
    opacity: 0.7;
    margin-bottom: 100px;
    cursor: pointer;

    &:hover {
        opacity: 1;
    }
`;

export const TwitterBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #1486cc;
    height: 30%;
    border-radius: 15px;
    opacity: 0.7;
    margin-bottom: 100px;
    cursor: pointer;

    &:hover {
        opacity: 1;
    }
`;

export const GithubBox = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #686f84;
    height: 30%;
    border-radius: 15px;
    opacity: 0.7;
    margin-bottom: 100px;
    cursor: pointer;

    &:hover {
        opacity: 1;
    }
`;

export const ActionContainer = styled.div`
    position: absolute;
    height: 100%;
    width: 50%;
    top: 0%;
    left: 0%;
    border-right: 5px solid black;

    color: awhitection;
    text-align: center;
    line-height: ${props => props.line};
    font-size: ${props => props.size};
    font-weight: bold;
    display: inline-block;
    user-select: none;
    padding: 20px;    
`;

export const ReactionContainer = styled.div`
    position: absolute;
    height: 100%;
    width: 50%;
    top: 0%;
    left: 50%;

    color: white;
    text-align: center;
    line-height: ${props => props.line};
    font-size: ${props => props.size};
    font-weight: bold;
    display: inline-block;
    user-select: none;
    padding: 20px;    
`;