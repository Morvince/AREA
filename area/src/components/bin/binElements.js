import styled from "styled-components";

export const Bin = styled.div`
width: 30px;
height: 600px;
z-index: 0;
position: fixed;
top:  220px;
left: 1880px;
background: ${props => props.color};
border-radius: 25% 0% 0% 25%;
`;

export const Bin2 = styled.div`
width: 20px;
height: 600px;
z-index: 100;
position: fixed;
top:  220px;
left: 1890px;
background: ${props => props.color};
border-radius: 25% 0% 0% 25%;
`;

export const Bin3 = styled.div`
width: 20px;
height: 600px;
z-index: 100;
position: fixed;
top:  220px;
left: 1910px;
background: white;
`;
