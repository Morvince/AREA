import React, { useEffect, useState } from 'react';
import { Dropdown } from 'react-native-element-dropdown';
import { useDropdownlistGetData } from '../../../api/apiCreatePage';
import styles from '../styles';
import { white } from '../../../color';

const DropDownList = ({name, uri, defaultValue, saveDataDropdownlistCurrentAction}) => {
  const [value, setValue] = useState(defaultValue);
  const [isFocus, setIsFocus] = useState(false);
  const [data, setData] = useState([])
  const dropdownlistGetData = useDropdownlistGetData()

  useEffect(() => {
    dropdownlistGetData.mutate(uri, {
      onSuccess: (data) => {
        setData(data.data.items)
      }
    })
  }, [])

  return (
    <Dropdown
      style={[styles.sideBarDropdownlist, isFocus && { borderWidth: 2 }]}
      placeholderStyle={{fontSize: 16, color: white}}
      selectedTextStyle={{fontSize: 16, color: white}}
      inputSearchStyle={{height: 40, fontSize: 16}}
      iconStyle={{width: 20, height: 20, tintColor: white}}
      data={data}
      search
      maxHeight={300}
      labelField="name"
      valueField="id"
      placeholder={!isFocus ? name : '...'}
      searchPlaceholder="Search..."
      value={value}
      onFocus={() => setIsFocus(true)}
      onBlur={() => setIsFocus(false)}
      onChange={item => {
        setValue(item.id);
        saveDataDropdownlistCurrentAction(name, item.id)
        setIsFocus(false);
      }}
    />
  );
};

export default DropDownList;