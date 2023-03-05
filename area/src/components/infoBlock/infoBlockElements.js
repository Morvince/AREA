import styled from "styled-components";

export const InfoBlockContainer = styled.div`
  width: 250px;
  min-height: 125px;
  position: absolute;
  top: ${(props) => props.top};
  left: ${(props) => props.left};
  background: ${(props) => props.background};
  border-radius: 25px;
  box-shadow: 5px 5px 1px 0 #373b48;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
`;

export const InfoWrapper = styled.div`
  width: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
`;

export const InfoTitle = styled.div`
  font-size: 1.5rem;
  color: #fff;
  text-align: center;
  margin-bottom: 5px;
  font-family: 'Calistoga', cursive;
  font-size: 18px;
  font-weight: 600;
  padding-left: 10px;
  padding-right: 10px;
  padding-top: 10px;
`;

export const InfoAction = styled.div`
  font-size: 1.5rem;
  color: #373b48;
  text-align: center;
  font-family: 'Calistoga', cursive;
  font-size: 18px;
  font-weight: 600;
  padding : 0px 10px 10px 10px;
  width: 93%;
`;

export const InputBox = styled.input`
  width: 95%;
  border: none;
  background: #fff;
  color: #373b48;
  font-size: 1.5rem;
  text-align: center;
  font-family: 'Calistoga', cursive;
  font-size: 18px;
  font-weight: 600;
  outline: none;
  border-radius: 4px;
  &::placeholder {
    color: #373b48;
  }

  &:focus {
    border-bottom: 1px solid #373b48;
  }

  &:focus::placeholder {
    color: transparent;
  }
`;

export const LittleBorder = styled.div`
  width: 75%;
  height: 1px;
  background: #fff;
  margin: 5px 0px;
`;