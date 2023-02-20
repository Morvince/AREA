import styled from "styled-components";

export const InfoBlockContainer = styled.div`
  width: 250px;
  height: 125px;
  position: absolute;
  top: 100px;
  left: 100px;
  background: #373b48;
  border-radius: 25px;
  box-shadow: 5px 5px 1px 0 #373b48;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
`;

export const InfoWrapper = styled.div`
  width: 100%;
  height: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
`;

export const InfoTitle = styled.div`
  font-size: 1.5rem;
  color: #fff;
  text-align: center;
  margin-bottom: 10px;
  font-family: 'Calistoga', cursive;
  font-size: 18px;
  font-weight: 600;
`;

export const InfoAction = styled.div`
  font-size: 1.5rem;
  color: #fff;
  text-align: center;
  font-family: 'Calistoga', cursive;
  font-size: 18px;
  font-weight: 600;
`;

export const InputBox = styled.input`
  width: 80%;
  height: 100%;
  border: none;
  background: #fff;
  color: #373b48;
  font-size: 1.5rem;
  text-align: center;
  font-family: 'Calistoga', cursive;
  font-size: 18px;
  font-weight: 600;
  outline: none;

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