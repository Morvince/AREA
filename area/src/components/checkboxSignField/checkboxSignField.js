import styled from 'styled-components'
import {darkPurple, lightPurple} from '../../color'

export const LabelCheckboxField = styled.label`
  font-size: 0.90em;
  margin-left: 6px;
  font-weight: bold;
  color: ${darkPurple};
`;

export const CheckboxField = styled.input`
  width: 17px;
  height: 17px;
  &:hover {
    cursor: pointer;
  }
  &:hover ~ label {
    color: ${lightPurple};
  }
`;