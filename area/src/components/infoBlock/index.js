import React from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox } from './infoBlockElements'
import Dropdown from 'react-dropdown';
import 'react-dropdown/style.css';

const InfoBlock = () => {

  const options = [
    { value: 'one', label: 'One' },
    { value: 'two', label: 'Two', className: 'myOptionClassName' },
    {
     type: 'group', name: 'group1', items: [
       { value: 'three', label: 'Three', className: 'myOptionClassName' },
       { value: 'four', label: 'Four' }
     ]
    },
    {
     type: 'group', name: 'group2', items: [
       { value: 'five', label: 'Five' },
       { value: 'six', label: 'Six' }
     ]
    }
  ];
  const defaultOption = options[0];

  return (
    <InfoBlockContainer>
      <InfoWrapper>
        <InfoTitle>Titre</InfoTitle>
        <InfoAction>
          <InputBox id="mail" type="text" placeholder="Name" />
        </InfoAction>
          <Dropdown options={options} value={defaultOption} placeholder="Select an option" />
      </InfoWrapper>
    </InfoBlockContainer>
  )
}

export default InfoBlock
