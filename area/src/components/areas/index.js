import React, { useState } from 'react'
import { AreaName, AreaZone, ArrowArea, BgColor, BlankZone, ButtonDelete, ButtonEdit, NumberOfAreasText, AreaContainer } from './areasElements';
import { Icon } from '@iconify/react';

const EditAreas = () => {
  const [showContainer, setShowContainer] = useState(false);
  const numberOfArea = 1;

  const handleClick = () => {
    setShowContainer(!showContainer);
  }

  return (
    <BgColor>
      <BlankZone>
        <NumberOfAreasText> You have exactly {numberOfArea} Area{numberOfArea > 1 ? 's' : ''} running ! </NumberOfAreasText>
        <AreaZone> 
          <AreaName> Area1</AreaName>
          <ArrowArea onClick={handleClick}> 
            <Icon icon="material-symbols:arrow-drop-down-sharp" width="90" style={{ position: 'absolute', color: "white", top: "50%", left: "50%", transform: "translate(-50%, -50%)"}} />
          </ArrowArea>
          {showContainer && (
            <AreaContainer>
              <ButtonDelete> Delete </ButtonDelete>
              <ButtonEdit> Edit</ButtonEdit>
              <p>Write info about the actual Area</p>
            </AreaContainer>
          )}
        </AreaZone>
      </BlankZone>
    </BgColor>
  );
};

export default EditAreas