import React from 'react';
import {
	List, 
	Edit, 
	Create,
	Datagrid, 
	ReferenceField, 
	TextField,
	DateField,
	EditButton, 
	DisabledInput, 
	LongTextInput, 
	ReferenceInput, 
	SelectInput, 
	SimpleForm, 
	TextInput,
	DateInput,
	RadioButtonGroupInput} from 'admin-on-rest';

export const PostList = (props) => (
    <List {...props}>
		<Datagrid>
			<TextField source="id" />
			<ReferenceField 
				label="Category" 
				source="categoryId" 
				reference="categories">
				<TextField source="title" />
			</ReferenceField>
			<DateField source="created" />	
			<TextField source="title" />
			<EditButton />
		</Datagrid>
    </List>
);

const PostTitle = ({ record }) => {
    return <span>Post {record ? `"${record.title}"` : ''}</span>;
};

export const PostEdit = (props) => (
    <Edit title={<PostTitle />} {...props}>
        <SimpleForm>
            <DisabledInput source="id" />
			<DateInput 
				label="Publication date" 
				source="created"
				locales="ru-RU"		
				showTime
				/>
            <ReferenceInput 
				label="Category" 
				source="categoryId" 
				reference="categories">
                <SelectInput optionText="title" />
            </ReferenceInput>
            <TextInput source="title" />
            <LongTextInput source="text" />
        </SimpleForm>
    </Edit>
);

export const PostCreate = (props) => (
    <Create {...props}>
        <SimpleForm>
            
            <TextInput source="title" />
            <LongTextInput source="text" />
			<ReferenceInput 
				label="Category" 
				source="categoryId" 
				reference="categories" 
				allowEmpty>
                <RadioButtonGroupInput optionText="title" />
            </ReferenceInput>
        </SimpleForm>
    </Create>
);