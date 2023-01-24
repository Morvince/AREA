import styled from 'styled-components'
import { lightPurple, darkPurple, white } from '../../color';

export const SignMessageWrapper = styled.div`
  margin-right: 80px;
  margin-left: -300px;
`;

export const SignMessageContainer = styled.div`
  padding: 18px 50px;
  background: linear-gradient(120deg, transparent, ${lightPurple}, ${darkPurple} 94%, transparent);
  border-radius: 40px;
  color: ${white};
  font-size: 18px;
  text-align: center;
  transform: translateY(-50%);
  transition-property: transform, opacity;
  transition-duration: 0.6s;
`;